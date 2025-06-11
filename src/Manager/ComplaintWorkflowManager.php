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

readonly class ComplaintWorkflowManager
{

    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface    $messageBus,
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

        if (!$transition)
            throw new \LogicException(sprintf(
                'No valid transition found for action "%s" from step "%s" on complaint ID %d.',
                $complaint->getId(),
                $complaint->getCurrentWorkflowStep() ? $complaint->getCurrentWorkflowStep()->getName() : 'NULL',
                $action->getName()
            ));

        if ($transition->getRoleRequired() && !$this->security->isGranted($transition->getRoleRequired()->getPermissions(), $currentUser)) {
            throw new \LogicException(sprintf(
                'user "%s" is not allowed to perform action "%s".',
                $transition->getRoleRequired()->getLabel(),
                $action->getName()
            ));
        }

        $uiConfig = $complaint->getCurrentWorkflowStep()->getUiConfiguration();
        if ($uiConfig && $uiConfig->getInputFields()) {
            $this->validateDynamicFields($data, $uiConfig->getInputFields());
        }

        if ($action->isRequiresComment() && empty($data['comment']))
            throw new \InvalidArgumentException('Comment is required for this action.');

        if ($action->isRequiresFile() && empty($data['file']))
            throw new \InvalidArgumentException('File is required for this action.');

        $oldStep = $complaint->getCurrentWorkflowStep();
        $newStep = $transition->getToStep();

        $complaint->setCurrentWorkflowStep($newStep);

        $nextExpectedAction = $this->findNextExpectedAction($newStep);
        $complaint->setCurrentWorkflowAction($nextExpectedAction);

        if (isset($data['justification']))
            $complaint->setReceivabilityDecisionJustification($data['justification']);

        if (isset($data['internalResolutionDecisionId'])) {
            $decisionParam = $this->em->getRepository(GeneralParameter::class)->findOneBy([
                'id' => $data['internalResolutionDecisionId'],
                'category' => GeneralParameterCategory::INTERNAL_DECISION
            ]);
            if ($decisionParam)
                $complaint->setInternalResolutionDecision($decisionParam);
        }

        if (isset($data['file']) && $data['file'] instanceof UploadedFile) {
            $attachedFile = new AttachedFile();
            $attachedFile->setComplaint($complaint);
            $attachedFile->setFileName($data['file']->getClientOriginalName());
            $attachedFile->setFileSize($data['file']->getSize());
            $attachedFile->setMimeType($data['file']->getMimeType());

            $fileTypeCategory = 'Document';
            if (str_starts_with($data['file']->getMimeType(), 'image/')) {
                $fileTypeCategory = 'Image';
            } elseif (str_starts_with($data['file']->getMimeType(), 'video/')) {
                $fileTypeCategory = 'Video';
            } elseif (str_starts_with($data['file']->getMimeType(), 'audio/')) {
                $fileTypeCategory = 'Audio';
            }
            $fileTypeParam = $this->em->getRepository(GeneralParameter::class)->findOneBy(['category' => GeneralParameterCategory::FILE_TYPE, 'value' => $fileTypeCategory]);
            if (!$fileTypeParam)
                throw new \Exception(sprintf('Not found file type parameter for category "%s".', $fileTypeCategory));
            $attachedFile->setFileType($fileTypeParam);

            $attachedFile->setWorkflowStep($newStep);
            $attachedFile->setUploadedBy($currentUser);
            $attachedFile->setUploadedAt(new \DateTimeImmutable());

            $attachedFile->setFile($data['file']);

            $this->em->persist($attachedFile);
        }

        $history = new ComplaintHistory();
        $history->setComplaint($complaint);
        $history->setOldWorkflowStep($oldStep);
        $history->setNewWorkflowStep($newStep);
        $history->setAction($action);
        $history->setComments($data['comment'] ?? null);
        $history->setActor($currentUser);
        $history->setActionDate(new \DateTimeImmutable());
        $this->em->persist($history);

        $this->em->flush();

        $this->messageBus->dispatch(new ComplaintWorkflowMessage($complaint->getId(), $action->getName(), $newStep->getName()));

        return $complaint;
    }

    private function validateDynamicFields(array $data, array $inputFieldsConfig): void
    {
        $constraints = new Assert\Collection([
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

            if ($fieldRequired)
                $fieldConstraints[] = new Assert\NotBlank(null, sprintf('%s est requis.', $fieldLabel));

            foreach ($validationRules as $rule) {
                if (is_string($rule)) {
                    switch ($rule) {
                        case 'not_blank':
                            break;
                        case 'email':
                            $fieldConstraints[] = new Assert\Email(null, sprintf('%s doit être une adresse email valide.', $fieldLabel));
                            break;
                        case 'numeric':
                            $fieldConstraints[] = new Assert\Type(['type' => 'numeric'], sprintf('%s doit être numérique.', $fieldLabel));
                            break;
                    }
                } elseif (is_array($rule)) {
                    foreach ($rule as $ruleName => $ruleValue) {
                        switch ($ruleName) {
                            case 'min_length':
                                $fieldConstraints[] = new Assert\Length(['min' => $ruleValue], sprintf('%s doit contenir au moins %s caractères.', $fieldLabel, $ruleValue));
                                break;
                            case 'max_length':
                                $fieldConstraints[] = new Assert\Length(['max' => $ruleValue], sprintf('%s doit contenir au plus %s caractères.', $fieldLabel, $ruleValue));
                                break;
                            case 'min':
                                $fieldConstraints[] = new Assert\GreaterThanOrEqual(['value' => $ruleValue], sprintf('%s doit être supérieur ou égal à %s.', $fieldLabel, $ruleValue));
                                break;
                            case 'max':
                                $fieldConstraints[] = new Assert\LessThanOrEqual(['value' => $ruleValue], sprintf('%s doit être inférieur ou égal à %s.', $fieldLabel, $ruleValue));
                                break;
                        }
                    }
                }
            }

            if ($fieldType === 'select' && isset($fieldConfig['optionsCategory'])) {
                $optionsCategory = $fieldConfig['optionsCategory'];
                $fieldConstraints[] = new Assert\Callback([
                    'callback' => function ($value, $context) use ($optionsCategory, $fieldLabel) {
                        if (null === $value && !$context->getConstraint()->getRequired())
                            return;
                        if (!$this->em->getRepository(GeneralParameter::class)->findOneBy(['id' => $value, 'category' => $optionsCategory]))
                            $context->buildViolation(sprintf('The option "%s" does not exist in category "%s".', $fieldLabel, $optionsCategory))
                                ->addViolation();
                    },
                    'payload' => ['required' => $fieldRequired]
                ]);
            }


            $constraints->fields[$fieldName] = $fieldConstraints;
        }

        $violations = $this->validator->validate($data, $constraints);

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
