<?php

namespace App\Manager;

use App\Constant\GeneralParameterCategory;
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
    )
    {
    }


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
                'No valid transition found for action "%s" from step "%s" on complaint ID %d.',
                $complaint->getId(),
                $complaint->getCurrentWorkflowStep() ? $complaint->getCurrentWorkflowStep()->getName() : 'NULL',
                $action->getName() ?: 'NULL'
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
        if ($uiConfig && $uiConfig->getInputFields()) {
            $this->validateDynamicFields($data, $uiConfig->getInputFields());
        }

        $extractedFields = $this->extractAndHydrateDynamicFields($data, $uiConfig->getInputFields() ?? []);

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

        if (isset($extractedFields['description'])) {
            $complaint->setDescription($extractedFields['description']);
        }
        if (isset($extractedFields['locationDetail'])) {
            $complaint->setLocationDetail($extractedFields['locationDetail']);
        }
        if (isset($extractedFields['latitude'])) {
            $complaint->setLatitude($extractedFields['latitude']);
        }
        if (isset($extractedFields['longitude'])) {
            $complaint->setLongitude($extractedFields['longitude']);
        }
        if (isset($extractedFields['receivabilityDecisionJustification'])) {
            $complaint->setReceivabilityDecisionJustification($extractedFields['receivabilityDecisionJustification']);
        }
        if (isset($extractedFields['meritsAnalysis'])) {
            $complaint->setMeritsAnalysis($extractedFields['meritsAnalysis']);
        }
        if (isset($extractedFields['resolutionProposal'])) {
            $complaint->setResolutionProposal($extractedFields['resolutionProposal']);
        }
        if (isset($extractedFields['internalDecisionComments'])) {
            $complaint->setInternalDecisionComments($extractedFields['internalDecisionComments']);
        }
        if (isset($extractedFields['executionActionsDescription'])) {
            $complaint->setExecutionActionsDescription($extractedFields['executionActionsDescription']);
        }
        if (isset($extractedFields['personInChargeOfExecution'])) {
            $complaint->setPersonInChargeOfExecution($extractedFields['personInChargeOfExecution']);
        }
        if (isset($extractedFields['satisfactionFollowUpComments'])) {
            $complaint->setSatisfactionFollowUpComments($extractedFields['satisfactionFollowUpComments']);
        }
        if (isset($extractedFields['escalationComments'])) {
            $complaint->setEscalationComments($extractedFields['escalationComments']);
        }
        if (isset($extractedFields['closureReason'])) {
            $complaint->setClosureReason($extractedFields['closureReason']);
        }
        if (isset($extractedFields['proposedResolutionDescription'])) {
            $complaint->setProposedResolutionDescription($extractedFields['proposedResolutionDescription']);
        }
        if (isset($extractedFields['incidentDate'])) {
            try {
                $complaint->setIncidentDate(new \DateTimeImmutable($extractedFields['incidentDate']));
            } catch (\Exception $e) {

            }
        }
        if (isset($extractedFields['closureDate'])) {
            try {
                $complaint->setClosureDate(new \DateTimeImmutable($extractedFields['closureDate']));
            } catch (\Exception $e) {

            }
        }

        if (isset($extractedFields['complaintType'])) {
            $complaint->setComplaintType($extractedFields['complaintType']);
        }
        if (isset($extractedFields['incidentCause'])) {
            $complaint->setIncidentCause($extractedFields['incidentCause']);
        }
        if (isset($extractedFields['roadAxis'])) {
            $complaint->setRoadAxis($extractedFields['roadAxis']);
        }
        if (isset($extractedFields['location'])) {
            $complaint->setLocation($extractedFields['location']);
        }
        if (isset($extractedFields['internalResolutionDecision'])) {
            $complaint->setInternalResolutionDecision($extractedFields['internalResolutionDecision']);
        }
        if (isset($extractedFields['complainantDecision'])) {
            $complaint->setComplainantDecision($extractedFields['complainantDecision']);
        }
        if (isset($extractedFields['satisfactionFollowUpResult'])) {
            $complaint->setSatisfactionFollowUpResult($extractedFields['satisfactionFollowUpResult']);
        }
        if (isset($extractedFields['escalationLevel'])) {
            $complaint->setEscalationLevel($extractedFields['escalationLevel']);
        }
        if (isset($extractedFields['complainant'])) {
            $complaint->setComplainant($extractedFields['complainant']);
        }
        if (isset($extractedFields['assignedTo'])) {
            $complaint->setAssignedTo($extractedFields['assignedTo']);
        }
        if (isset($extractedFields['currentAssignee'])) {
            $complaint->setCurrentAssignee($extractedFields['currentAssignee']);
        }
        if (isset($extractedFields['involvedCompany'])) {
            $complaint->setInvolvedCompany($extractedFields['involvedCompany']);
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
            if (!$fileTypeParam) {
                throw new \Exception(sprintf('Not found file type parameter for category "%s".', $fileTypeCategory));
            }

            $attachedFile
                ->setFileType($fileTypeParam)
                ->setWorkflowStep($newStep)
                ->setUploadedBy($currentUser)
                ->setUploadedAt(new \DateTimeImmutable())
                ->setFile($file);
            $this->em->persist($attachedFile);
        }

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

            if ($fieldValue === null) {
                continue;
            }

            switch ($fieldType) {
                case 'select':
                    if (is_string($fieldValue)) {
                        $id = $this->extractIdFromIri($fieldValue);
                        if ($id === null) {
                            continue 2;
                        }

                        if (isset($fieldConfig['optionsCategory'])) {
                            $category = $fieldConfig['optionsCategory'];
                            $entity = $this->em->getRepository(GeneralParameter::class)->findOneBy(['id' => $id, 'category' => $category]);
                            if ($entity) {
                                $extracted[$fieldName] = $entity;
                            }
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
                            if ($entity) {
                                $extracted[$fieldName] = $entity;
                            }
                        }
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
                        $extracted[$fieldName] = new \DateTimeImmutable($fieldValue);
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
        $constraintsCollection = new Assert\Collection([
            'fields' => [],
            'allowExtraFields' => true,
            'allowMissingFields' => true,
        ]);

        foreach ($inputFieldsConfig as $fieldConfig) {
            $fieldName = $fieldConfig['name'];
            $fieldLabel = $fieldConfig['label'] ?? $fieldName;
            $fieldRequired = $fieldConfig['required'] ?? false;
            $validationRules = $fieldConfig['validationRules'] ?? [];
            $fieldType = $fieldConfig['type'] ?? 'text';

            $fieldConstraints = [];

            if ($fieldRequired) {
                if ($fieldType !== 'file') {
                    $fieldConstraints[] = new Assert\NotBlank(null, sprintf('%s is required.', $fieldLabel));
                }
            }


            foreach ($validationRules as $rule) {
                if (is_string($rule)) {
                    switch ($rule) {
                        case 'not_blank':
                            if (!$fieldRequired) {
                                $fieldConstraints[] = new Assert\NotBlank(null, sprintf('%s cannot be blank.', $fieldLabel));
                            }
                            break;
                        case 'email':
                            $fieldConstraints[] = new Assert\Email(null, sprintf('%s must be a valid email address.', $fieldLabel));
                            break;
                        case 'numeric':
                            $fieldConstraints[] = new Assert\Type(['type' => 'numeric'], sprintf('%s must be numeric.', $fieldLabel));
                            break;
                    }
                } elseif (is_array($rule)) {
                    foreach ($rule as $ruleName => $ruleValue) {
                        if ($ruleName == 'required_if') {
                        }
                    }
                }
            }

            switch ($fieldType) {
                case 'select':
                    $fieldConstraints[] = new Assert\Callback([
                        'callback' => function ($iri, $context) use ($fieldConfig, $fieldLabel, $fieldRequired) {
                            if (null === $iri || (is_string($iri) && trim($iri) === '')) {
                                if (!$fieldRequired) return;
                            }

                            if (!is_string($iri)) {
                                $context->buildViolation(sprintf('Expected IRI string for %s, got %s.', $fieldLabel, gettype($iri)))
                                    ->addViolation();
                                return;
                            }

                            $id = $this->extractIdFromIri($iri);
                            if (null === $id) {
                                $context->buildViolation(sprintf('Invalid IRI format for %s: %s.', $fieldLabel, $iri))
                                    ->addViolation();
                                return;
                            }

                            $foundEntity = null;
                            if (isset($fieldConfig['optionsCategory'])) {
                                $foundEntity = $this->em->getRepository(GeneralParameter::class)->findOneBy(['id' => $id, 'category' => $fieldConfig['optionsCategory']]);
                            } elseif (isset($fieldConfig['optionsResource'])) {
                                switch ($fieldConfig['optionsResource']) {
                                    case 'api/companies':
                                        $foundEntity = $this->em->getRepository(Company::class)->find($id);
                                        break;
                                    case 'api/users':
                                        $foundEntity = $this->em->getRepository(User::class)->find($id);
                                        break;
                                    case 'api/road_axes':
                                        $foundEntity = $this->em->getRepository(RoadAxis::class)->find($id);
                                        break;
                                    case 'api/locations':
                                        $foundEntity = $this->em->getRepository(Location::class)->find($id);
                                        break;
                                    case 'api/complainants':
                                        $foundEntity = $this->em->getRepository(Complainant::class)->find($id);
                                        break;
                                    default:
                                        $context->buildViolation(sprintf('Unsupported resource "%s" for %s.', $fieldConfig['optionsResource'], $fieldLabel))
                                            ->addViolation();
                                        return;
                                }
                            }

                            if (!$foundEntity) {
                                $context->buildViolation(sprintf('The selected option for %s (ID: %s, IRI: %s) does not exist or is not valid.', $fieldLabel, $id, $iri))
                                    ->addViolation();
                            }
                        }
                    ]);
                    break;

                case 'file':
                    $fieldConstraints[] = new Assert\File([
                        'maxSize' => '10M',
                        'mimeTypes' => ['image/*', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
                        'mimeTypesMessage' => 'Please upload a valid image, PDF, or document.',
                    ]);
                    if ($fieldRequired) {
                        $fieldConstraints[] = new Assert\NotNull(null, sprintf('%s is required.', $fieldLabel));
                    }
                    break;

                case 'date':
                    $fieldConstraints[] = new Assert\Type(['type' => \DateTimeImmutable::class], sprintf('%s must be a valid date.', $fieldLabel));
                    break;
                case 'boolean':
                case 'checkbox':
                    $fieldConstraints[] = new Assert\Type(['type' => 'bool'], sprintf('%s must be a boolean.', $fieldLabel));
                    break;
            }

            $constraintsCollection->fields[$fieldName] = $fieldConstraints;
        }

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
