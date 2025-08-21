<?php

namespace App\Manager;

use App\Constant\GeneralParameterCategory;
use App\Constant\WorkflowStepName;
use App\Entity\AttachedFile;
use App\Entity\Complaint;
use App\Entity\ComplaintHistory;
use App\Entity\GeneralParameter;
use App\Entity\User;
use App\Entity\WorkflowAction;
use App\Entity\WorkflowStep;
use App\Entity\WorkflowTransition;
use App\Message\ComplaintWorkflowMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Company;
use App\Entity\RoadAxis;
use App\Entity\Location;
use App\Entity\Complainant;

readonly class ComplaintWorkflowManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface    $bus,
        private Security               $security,
        private ValidatorInterface     $validator,
        private AssignmentManager      $assignmentManager
    )
    {
    }

    /**
     * @throws ExceptionInterface
     */
    public function applyAction(Complaint $complaint, WorkflowAction $action, array $data = []): Complaint
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        $transition = $this->em->getRepository(WorkflowTransition::class)->findOneBy([
            'fromStep' => $complaint->getCurrentWorkflowStep(),
            'action' => $action,
        ]);

        if (!$transition) {
            throw new \LogicException(sprintf(
                'No valid transition found for action "%s" from step "%s" on complaint ID %s.',
                $action->getName() ?: 'NULL',
                $complaint->getCurrentWorkflowStep() ? $complaint->getCurrentWorkflowStep()->getName() : 'NULL',
                $complaint->getId()
            ));
        }

        $requiredRoles = $transition->getRoleRequired();

        if (!empty($requiredRoles)) {
            $userRoles = $currentUser->getRoles();
            if (empty(array_intersect($userRoles, $requiredRoles))) {
                throw new \LogicException(sprintf(
                    'User "%s" is not allowed to perform action "%s". Missing required roles. User roles: %s, Required roles: %s',
                    $currentUser->getDisplayName(),
                    $action->getName(),
                    json_encode($userRoles),
                    json_encode($requiredRoles)
                ));
            }
        }

        $uiConfig = $complaint->getCurrentWorkflowStep()->getUiConfiguration();
        $inputFields = [];
        if ($uiConfig && $uiConfig->getInputFields()) {
            $allInputFields = $uiConfig->getInputFields();
            $inputFields = array_filter($allInputFields, function ($field) {
                return isset($field['name']) && $field['name'] !== 'assignmentType';
            });
            $this->validateDynamicFields($data, $inputFields);
        }

        $extractedFields = $this->extractAndHydrateDynamicFields($data, $inputFields);

        $comment = $extractedFields['comment'] ?? null;
        $file = $extractedFields['file'] ?? null;

        if ($action->isRequiresComment() && empty($comment)) {
            throw new \InvalidArgumentException('Comment is required for this action.');
        }

        if ($action->isRequiresFile() && empty($file)) {
            throw new \InvalidArgumentException('File is required for this action.');
        }

        $oldStep = $complaint->getCurrentWorkflowStep();
        $newStep = $transition->getToStep();

        $complaint->setCurrentWorkflowStep($newStep);

        $nextExpectedAction = $this->findNextExpectedAction($newStep);
        $complaint->setCurrentWorkflowAction($nextExpectedAction);

        $fieldSetterMap = [
            'description' => 'setDescription',
            'locationDetail' => 'setLocationDetail',
            'latitude' => 'setLatitude',
            'longitude' => 'setLongitude',
            'isReceivable' => 'setIsReceivable',
            'receivabilityDecisionJustification' => 'setReceivabilityDecisionJustification',
            'meritsAnalysis' => 'setMeritsAnalysis',
            'resolutionProposal' => 'setResolutionProposal',
            'internalDecisionComments' => 'setInternalDecisionComments',
            'executionActionsDescription' => 'setExecutionActionsDescription',
            'personInChargeOfExecution' => 'setPersonInChargeOfExecution',
            'satisfactionFollowUpComments' => 'setSatisfactionFollowUpComments',
            'escalationComments' => 'setEscalationComments',
            'closureReason' => 'setClosureReason',
            'proposedResolutionDescription' => 'setProposedResolutionDescription',
            'incidentDate' => 'setIncidentDate',
            'closureDate' => 'setClosureDate',
            'complaintType' => 'setComplaintType',
            'roadAxis' => 'setRoadAxis',
            'location' => 'setLocation',
            'internalResolutionDecision' => 'setInternalResolutionDecision',
            'complainantDecision' => 'setComplainantDecision',
            'satisfactionFollowUpResult' => 'setSatisfactionFollowUpResult',
            'escalationLevel' => 'setEscalationLevel',
            'complainant' => 'setComplainant',
            'assignedTo' => 'setAssignedTo',
            'currentAssignee' => 'setCurrentAssignee',
            'involvedCompany' => 'setInvolvedCompany',
            'closed' => 'setClosed',
        ];

        foreach ($fieldSetterMap as $fieldName => $setterMethod) {
            if (array_key_exists($fieldName, $extractedFields)) {
                $complaint->$setterMethod($extractedFields[$fieldName]);
            }
        }

        if (array_key_exists('incidentCauses', $extractedFields)) {
            $complaint->getIncidentCauses()->clear();
            if (is_iterable($extractedFields['incidentCauses'])) {
                foreach ($extractedFields['incidentCauses'] as $incidentCause) {
                    $complaint->addIncidentCause($incidentCause);
                }
            }
        }

        if ($action->getName() === 'verify_receivability_action') {
            $complaint->setIsReceivable(true);
        }

        if ($action->getName() === 'mark_non_receivable') {
            $complaint->setIsReceivable(false);
        }

        if ($file instanceof UploadedFile) {
            $attachedFile = (new AttachedFile())
                ->setComplaint($complaint)
                ->setFileName($file->getClientOriginalName())
                ->setFileSize($file->getSize())
                ->setMimeType($file->getMimeType());

            $fileTypeCategory = 'Document';
            if (str_starts_with($file->getMimeType(), 'image/')) {
                $fileTypeCategory = 'Image';
            } elseif (str_starts_with($file->getMimeType(), 'video/')) {
                $fileTypeCategory = 'Video';
            } elseif (str_starts_with($file->getMimeType(), 'audio/')) {
                $fileTypeCategory = 'Audio';
            }

            $fileTypeParam = $this->em->getRepository(GeneralParameter::class)->findOneBy(['category' => GeneralParameterCategory::FILE_TYPE, 'value' => $fileTypeCategory]);
            if (!$fileTypeParam)
                throw new \Exception(sprintf('Not found file type parameter for category "%s".', $fileTypeCategory));

            $attachedFile->setFile($file);

            $attachedFile
                ->setFileType($fileTypeParam)
                ->setWorkflowStep($newStep)
                ->setUploadedBy($currentUser)
                ->setUploadedAt(new \DateTimeImmutable());

            $this->em->persist($attachedFile);
        }

        $this->assignmentManager->assignDefaultActor($complaint);

        $history = (new ComplaintHistory())
            ->setComplaint($complaint)
            ->setOldWorkflowStep($oldStep)
            ->setNewWorkflowStep($newStep)
            ->setAction($action)
            ->setComments($comment)
            ->setActor($currentUser)
            ->setActionDate(new \DateTimeImmutable());

        $this->em->persist($history);
        $this->em->flush();

        $this->bus->dispatch(new ComplaintWorkflowMessage($complaint->getId(), $action->getName(), $newStep->getName()));

        return $complaint;
    }

    private function extractIdFromIri(?string $iri): ?string
    {
        if (null === $iri) {
            return null;
        }
        $parts = explode('/', $iri);
        return end($parts) ?: null;
    }

    private function extractAndHydrateDynamicFields(array $data, array $inputFieldsConfig): array
    {
        $extracted = [];

        foreach ($inputFieldsConfig as $fieldConfig) {
            $fieldName = $fieldConfig['name'];
            $fieldType = $fieldConfig['type'] ?? 'text';
            $fieldValue = $data[$fieldName] ?? null;

            if (!array_key_exists($fieldName, $data)) {
                continue;
            }

            switch ($fieldType) {
                case 'select':
                    $isMultiple = $fieldConfig['multiple'] ?? false;
                    if ($isMultiple) {
                        $entities = [];
                        if (is_array($fieldValue)) {
                            foreach ($fieldValue as $iri) {
                                if (!is_string($iri)) continue;
                                $id = $this->extractIdFromIri($iri);
                                if ($id === null) continue;

                                $entity = null;
                                if (isset($fieldConfig['optionsCategory'])) {
                                    $category = $fieldConfig['optionsCategory'];
                                    $entity = $this->em->getRepository(GeneralParameter::class)->findOneBy(['id' => $id, 'category' => $category]);
                                } elseif (isset($fieldConfig['optionsResource'])) {
                                    $resource = $fieldConfig['optionsResource'];
                                    switch ($resource) {
                                        case 'api/companies':
                                            $entity = $this->em->getRepository(Company::class)->find($id);
                                            break;
                                        case 'api/users':
                                            $entity = $this->em->getRepository(User::class)->find($id);
                                            break;
                                        case 'api/road_axes':
                                            $entity = $this->em->getRepository(RoadAxis::class)->find($id);
                                            break;
                                        case 'api/locations':
                                            $entity = $this->em->getRepository(Location::class)->find($id);
                                            break;
                                        case 'api/complainants':
                                            $entity = $this->em->getRepository(Complainant::class)->find($id);
                                            break;
                                    }
                                }
                                if ($entity) {
                                    $entities[] = $entity;
                                }
                            }
                        }
                        $extracted[$fieldName] = $entities;
                    } elseif (is_string($fieldValue)) {
                        $id = $this->extractIdFromIri($fieldValue);
                        if ($id === null) {
                            $extracted[$fieldName] = null;
                            continue 2;
                        }

                        $entity = null;
                        if (isset($fieldConfig['optionsCategory'])) {
                            $category = $fieldConfig['optionsCategory'];
                            $entity = $this->em->getRepository(GeneralParameter::class)->findOneBy(['id' => $id, 'category' => $category]);
                        } elseif (isset($fieldConfig['optionsResource'])) {
                            $resource = $fieldConfig['optionsResource'];
                            switch ($resource) {
                                case 'api/companies':
                                    $entity = $this->em->getRepository(Company::class)->find($id);
                                    break;
                                case 'api/users':
                                    $entity = $this->em->getRepository(User::class)->find($id);
                                    break;
                                case 'api/road_axes':
                                    $entity = $this->em->getRepository(RoadAxis::class)->find($id);
                                    break;
                                case 'api/locations':
                                    $entity = $this->em->getRepository(Location::class)->find($id);
                                    break;
                                case 'api/complainants':
                                    $entity = $this->em->getRepository(Complainant::class)->find($id);
                                    break;
                            }
                        }
                        $extracted[$fieldName] = $entity;
                    } else {
                        $extracted[$fieldName] = $fieldValue;
                    }
                    break;

                case 'boolean':
                case 'checkbox':
                    $extracted[$fieldName] = filter_var($fieldValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    break;

                case 'number':
                    $extracted[$fieldName] = is_numeric($fieldValue) ? (float)$fieldValue : null;
                    break;

                case 'date':
                    try {
                        $extracted[$fieldName] = $fieldValue ? new \DateTimeImmutable($fieldValue) : null;
                    } catch (\Exception $e) {
                        $extracted[$fieldName] = null;
                    }
                    break;

                case 'file':
                    if ($fieldValue instanceof UploadedFile) {
                        $extracted[$fieldName] = $fieldValue;
                    }
                    break;

                case 'text':
                case 'textarea':
                default:
                    $extracted[$fieldName] = $fieldValue;
                    break;
            }
        }
        return $extracted;
    }


    private function validateDynamicFields(array $data, array $inputFieldsConfig): void
    {
        $constraintsForCollection = [];

        foreach ($inputFieldsConfig as $fieldConfig) {
            $fieldName = $fieldConfig['name'];
            $fieldLabel = $fieldConfig['label'] ?? $fieldName;
            $fieldRequired = $fieldConfig['required'] ?? false;
            $validationRules = $fieldConfig['validationRules'] ?? [];
            $fieldType = $fieldConfig['type'] ?? 'text';

            $fieldConstraints = [];

            if ($fieldRequired) {
                if (in_array($fieldType, ['file', 'boolean', 'checkbox'])) {
                    $fieldConstraints[] = new Assert\NotNull([
                        'message' => sprintf('%s is required.', $fieldLabel)
                    ]);
                } else {
                    $fieldConstraints[] = new Assert\NotBlank([
                        'message' => sprintf('%s is required.', $fieldLabel)
                    ]);
                }
            }

            foreach ($validationRules as $rule) {
                if (is_string($rule)) {
                    switch ($rule) {
                        case 'not_blank':
                            if (!$fieldRequired) {
                                if (in_array($fieldType, ['file', 'boolean', 'checkbox'])) {
                                    $fieldConstraints[] = new Assert\NotNull([
                                        'message' => sprintf('%s cannot be blank.', $fieldLabel)
                                    ]);
                                } else {
                                    $fieldConstraints[] = new Assert\NotBlank([
                                        'message' => sprintf('%s cannot be blank.', $fieldLabel)
                                    ]);
                                }
                            }
                            break;
                        case 'email':
                            $fieldConstraints[] = new Assert\Email([
                                'message' => sprintf('%s must be a valid email address.', $fieldLabel)
                            ]);
                            break;
                        case 'numeric':
                            $fieldConstraints[] = new Assert\Type([
                                'type' => 'numeric',
                                'message' => sprintf('%s must be numeric.', $fieldLabel)
                            ]);
                            break;
                    }
                }
            }

            switch ($fieldType) {
                case 'select':
                    $isMultiple = $fieldConfig['multiple'] ?? false;
                    $fieldConstraints[] = new Assert\Callback([
                        'callback' => function ($value, $context) use ($fieldConfig, $fieldLabel, $fieldRequired, $isMultiple) {
                            $iris = [];
                            if ($isMultiple) {
                                $iris = is_array($value) ? $value : [];
                            } elseif (!empty($value)) {
                                $iris = [$value];
                            }

                            if ($fieldRequired && empty($iris)) {
                                $context->buildViolation(sprintf('%s is required.', $fieldLabel))
                                    ->addViolation();
                                return;
                            }

                            foreach ($iris as $iri) {
                                if (!is_string($iri)) continue;

                                $id = $this->extractIdFromIri($iri);
                                if ($id === null) {
                                    $context->buildViolation(sprintf('%s has an invalid format.', $fieldLabel))
                                        ->addViolation();
                                    return;
                                }

                                $entityExists = false;
                                if (isset($fieldConfig['optionsCategory'])) {
                                    $category = $fieldConfig['optionsCategory'];
                                    $entity = $this->em->getRepository(GeneralParameter::class)->findOneBy(['id' => $id, 'category' => $category]);
                                    $entityExists = $entity !== null;
                                } elseif (isset($fieldConfig['optionsResource'])) {
                                    $resource = $fieldConfig['optionsResource'];
                                    $entity = null;
                                    switch ($resource) {
                                        case 'api/companies':
                                            $entity = $this->em->getRepository(Company::class)->find($id);
                                            break;
                                        case 'api/users':
                                            $entity = $this->em->getRepository(User::class)->find($id);
                                            break;
                                        case 'api/road_axes':
                                            $entity = $this->em->getRepository(RoadAxis::class)->find($id);
                                            break;
                                        case 'api/locations':
                                            $entity = $this->em->getRepository(Location::class)->find($id);
                                            break;
                                        case 'api/complainants':
                                            $entity = $this->em->getRepository(Complainant::class)->find($id);
                                            break;
                                    }
                                    $entityExists = $entity !== null;
                                }

                                if (!$entityExists) {
                                    $context->buildViolation(sprintf('%s refers to a non-existent entity.', $fieldLabel))
                                        ->addViolation();
                                }
                            }
                        }
                    ]);
                    break;

                case 'file':
                    $fieldConstraints[] = new Assert\File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'image/*',
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image, PDF, or document (Word/Excel).',
                    ]);
                    break;

                case 'boolean':
                case 'checkbox':
                    $fieldConstraints[] = new Assert\Type([
                        'type' => 'bool',
                        'message' => sprintf('%s must be a boolean value.', $fieldLabel)
                    ]);
                    break;
            }

            if (!empty($fieldConstraints)) {
                $constraintsForCollection[$fieldName] = $fieldConstraints;
            }
        }

        $constraintsCollection = new Assert\Collection([
            'fields' => $constraintsForCollection,
            'allowExtraFields' => true,
            'allowMissingFields' => true,
        ]);

        $violations = $this->validator->validate($data, $constraintsCollection);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            throw new \InvalidArgumentException('Form data is invalid : ' . implode('; ', $errors));
        }
    }

    private function findNextExpectedAction(WorkflowStep $step): ?WorkflowAction
    {
        $nextTransition = $this->em->getRepository(WorkflowTransition::class)->findOneBy(['fromStep' => $step], ['id' => 'ASC']);

        return $nextTransition ? $nextTransition->getAction() : null;
    }
}
