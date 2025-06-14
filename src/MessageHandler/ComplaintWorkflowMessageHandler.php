<?php

namespace App\MessageHandler;

use App\Constant\WorkflowStepName;
use App\Message\ComplaintWorkflowMessage;
use App\Message\ComplaintRegisteredMessage;
use App\Message\ComplaintClassifiedAssignedMessage;
use App\Message\ComplaintReceivabilityVerifiedMessage;
use App\Message\MeritsExaminedMessage;
use App\Message\ResolutionProposedMessage;
use App\Message\InternalDecisionMadeMessage;
use App\Message\ComplainantDecisionMessage;
use App\Message\ResolutionExecutedMessage;
use App\Message\SatisfactionFollowedUpMessage;
use App\Message\ComplaintEscalatedMessage;
use App\Message\ComplaintClosedMessage;
use App\Repository\ComplaintRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class ComplaintWorkflowMessageHandler
{
    public function __construct(
        private MessageBusInterface $bus,
        private ComplaintRepository $complaintRepository,
        private LoggerInterface     $logger
    )
    {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(ComplaintWorkflowMessage $message): void
    {
        $complaintId = $message->getComplaintId();
        $actionName = $message->getActionName();
        $newStepName = $message->getNewStepName();

        $this->logger->info(sprintf(
            "ComplaintWorkflowMessage received for complaint %s. Action: %s, New Step: %s",
            $complaintId, $actionName, $newStepName
        ));

        $complaint = $this->complaintRepository->find($complaintId);
        if (!$complaint) {
            $this->logger->error(sprintf("Complaint with ID %s not found for WorkflowMessage. Cannot dispatch specific messages.", $complaintId));
            return;
        }

        switch ($actionName) {
            case 'create_complaint_action':
                $this->bus->dispatch(new ComplaintRegisteredMessage(
                    $complaint->getId(),
                    $complaint->getComplainant()->getContactEmail(),
                    $complaint->getComplainant()->getContactPhone()
                ));
                break;

            case 'classify_assign_action':
                if ($complaint->getAssignedTo()) {
                    $this->bus->dispatch(new ComplaintClassifiedAssignedMessage(
                        $complaint->getId(),
                        $complaint->getAssignedTo()->getId()
                    ));
                }
                break;

            case 'verify_receivability_action':
                $isReceivable = ($newStepName === WorkflowStepName::RECEIVABLE);
                $this->bus->dispatch(new ComplaintReceivabilityVerifiedMessage(
                    $complaint->getId(),
                    $isReceivable,
                    $complaint->getComplainant()->getContactEmail(),
                    $complaint->getComplainant()->getContactPhone()
                ));
                break;

            case 'examine_merits_action':
                $this->bus->dispatch(new MeritsExaminedMessage(
                    $complaint->getId(),
                    $complaint->getMeritsAnalysis()
                ));
                break;

            case 'propose_resolution_action':
                $this->bus->dispatch(new ResolutionProposedMessage(
                    $complaint->getId()
                ));
                break;

            case 'internal_decision_action':
                if ($complaint->getInternalResolutionDecision()) {
                    $this->bus->dispatch(new InternalDecisionMadeMessage(
                        $complaint->getId(),
                        $complaint->getInternalResolutionDecision()->getId()
                    ));
                }
                break;

            case 'complainant_decision_action':
                if ($complaint->getComplainantDecision()) {
                    $this->bus->dispatch(new ComplainantDecisionMessage(
                        $complaint->getId(),
                        $complaint->getComplainantDecision()->getId()
                    ));
                }
                break;

            case 'execute_resolution_action':
                $this->bus->dispatch(new ResolutionExecutedMessage(
                    $complaint->getId()
                ));
                break;

            case 'satisfaction_follow_up_action':
                if ($complaint->getSatisfactionFollowUpResult()) {
                    $this->bus->dispatch(new SatisfactionFollowedUpMessage(
                        $complaint->getId(),
                        $complaint->getSatisfactionFollowUpResult()->getId()
                    ));
                }
                break;

            case 'escalate_action':
                if ($complaint->getEscalationLevel()) {
                    $this->bus->dispatch(new ComplaintEscalatedMessage(
                        $complaint->getId(),
                        $complaint->getEscalationLevel()->getId()
                    ));
                }
                break;

            case 'close_complaint_action':
                $this->bus->dispatch(new ComplaintClosedMessage(
                    $complaint->getId(),
                    $complaint->getClosureReason() ?? 'Reason not specified'
                ));
                break;

            // case 'generate_report_from_workflow_action':
            //     $this->bus->dispatch(new GenerateReportCommand(...));
            //     break;

            default:
                $this->logger->warning(sprintf(
                    "ComplaintWorkflowMessage with unhandled action '%s' for complaint %s in step %s. No specific message dispatched.",
                    $actionName, $complaintId, $newStepName
                ));
                break;
        }
    }
}
