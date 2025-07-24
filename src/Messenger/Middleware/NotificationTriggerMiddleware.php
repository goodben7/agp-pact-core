<?php

namespace App\Messenger\Middleware;

use App\Entity\Complaint;
use App\Entity\Notification;
use App\Entity\NotificationTemplate;
use App\Factory\NotificationFactory;
use App\Message\SendNotificationMessage;
use App\Repository\NotificationTemplateRepository;
use App\Repository\UserRepository;
use App\Service\StringTemplateRenderer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

readonly class NotificationTriggerMiddleware implements MiddlewareInterface
{
    public function __construct(
        private NotificationTemplateRepository $notificationTemplateRepository,
        private NotificationFactory            $notificationFactory,
        private MessageBusInterface            $messageBus,
        private StringTemplateRenderer         $templateRenderer,
        private EntityManagerInterface         $entityManager,
        private LoggerInterface                $logger,
        private UserRepository                 $userRepository
    )
    {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $envelope = $stack->next()->handle($envelope, $stack);

        if (!$envelope->last(HandledStamp::class)) {
            return $envelope;
        }

        $message = $envelope->getMessage();
        $messageFqcn = get_class($message);
        $this->logger->debug(sprintf('Middleware processing message: %s', $messageFqcn));

        $templates = $this->notificationTemplateRepository->findBy([
            'triggerEvent' => $messageFqcn,
            'active' => true
        ]);

        if (empty($templates)) {
            $this->logger->debug(sprintf('No active notification templates found for event: %s', $messageFqcn));
            return $envelope;
        }

        $complaint = $this->getComplaintFromMessage($message);

        foreach ($templates as $template) {
            try {
                if ($this->shouldSkipForSensibility($template, $complaint, $messageFqcn)) {
                    continue;
                }

                $recipients = $this->getRecipients($template, $complaint);

                if (empty($recipients)) {
                    $this->logger->warning(sprintf(
                        'Could not determine any valid recipient for template "%s" (ID: %s) triggered by %s.',
                        $template->getName(),
                        $template->getId(),
                        $messageFqcn
                    ));
                    continue;
                }

                $this->dispatchNotifications($template, $recipients, $complaint, $message);

            } catch (\Throwable $e) {
                $this->logger->error(sprintf(
                    'Error processing notification template "%s" (ID: %s) for event %s: %s',
                    $template->getName(),
                    $template->getId(),
                    $messageFqcn,
                    $e->getMessage()
                ), ['exception' => $e]);
            }
        }

        return $envelope;
    }

    private function getComplaintFromMessage(object $message): ?Complaint
    {
        if (!method_exists($message, 'getComplaintId')) {
            return null;
        }

        $complaintId = $message->getComplaintId();
        $complaint = $this->entityManager->getRepository(Complaint::class)->find($complaintId);

        if (!$complaint) {
            $this->logger->warning(sprintf('Complaint %s not found for notification triggering.', $complaintId));
            return null;
        }

        return $complaint;
    }

    private function shouldSkipForSensibility(NotificationTemplate $template, ?Complaint $complaint, string $messageFqcn): bool
    {
        if ($template->getIsSensitive() === null) {
            return false;
        }

        if (!$complaint) {
            $this->logger->warning(sprintf(
                'Skipping template "%s" because it has a sensibility constraint but no complaint was found for event %s.',
                $template->getName(),
                $messageFqcn
            ));
            return true;
        }

        if ($template->getIsSensitive() !== $complaint->getIsSensitive()) {
            $this->logger->info(sprintf(
                'Skipping notification for template "%s". Template sensibility (%s) does not match complaint sensibility (%s).',
                $template->getName(),
                var_export($template->getIsSensitive(), true),
                var_export($complaint->getIsSensitive(), true)
            ));
            return true;
        }

        return false;
    }

    private function getRecipients(NotificationTemplate $template, ?Complaint $complaint): array
    {
        $recipients = [];
        $sentVia = $template->getSentVia();
        $selectors = $template->getRecipientSelectors();

        if (empty($selectors)) {
            $this->logger->warning(sprintf(
                'Template "%s" (ID: %s) has no recipient selectors defined. No one to notify.',
                $template->getName(),
                $template->getId()
            ));
            return [];
        }

        foreach ($selectors as $selector) {
            match ($selector) {
                NotificationTemplate::RECIPIENT_PROFILE_USERS => $this->addProfileUsers($recipients, $template, $sentVia),
                NotificationTemplate::RECIPIENT_COMPLAINANT => $this->addComplainant($recipients, $complaint, $sentVia),
                NotificationTemplate::RECIPIENT_INVOLVED_COMPANY => $this->addInvolvedCompany($recipients, $complaint, $sentVia),
                default => $this->logger->warning(sprintf('Unknown recipient selector "%s" in template "%s".', $selector, $template->getName()))
            };
        }

        return array_unique($recipients, SORT_REGULAR);
    }

    private function addProfileUsers(array &$recipients, NotificationTemplate $template, string $sentVia): void
    {
        if ($profile = $template->getProfile()) {
            $users = $this->userRepository->findUsersByProfile($profile);
            foreach ($users as $user) {
                $this->addRecipient($recipients, $sentVia, $user->getEmail(), $user->getPhone());
            }
        } else {
            $this->logger->warning(sprintf(
                'Template "%s" has "%s" selector but no profile is associated.',
                $template->getName(),
                NotificationTemplate::RECIPIENT_PROFILE_USERS
            ));
        }
    }

    private function addComplainant(array &$recipients, ?Complaint $complaint, string $sentVia): void
    {
        if ($complaint && $complainant = $complaint->getComplainant()) {
            $this->addRecipient($recipients, $sentVia, $complainant->getContactEmail(), $complainant->getContactPhone());
        }
    }

    private function addInvolvedCompany(array &$recipients, ?Complaint $complaint, string $sentVia): void
    {
        if ($complaint && method_exists($complaint, 'getInvolvedCompany') && $involvedCompany = $complaint->getInvolvedCompany()) {
            $this->addRecipient($recipients, $sentVia, $involvedCompany->getContactEmail(), $involvedCompany->getContactPhone());
        }
    }

    private function addRecipient(array &$recipients, string $sentVia, ?string $email, ?string $phone): void
    {
        switch ($sentVia) {
            case Notification::SENT_VIA_EMAIL:
                if ($email) {
                    $recipients[] = ['address' => $email, 'type' => 'email'];
                }
                break;
            case Notification::SENT_VIA_SMS:
            case Notification::SENT_VIA_WHATSAPP:
                if ($phone) {
                    $recipients[] = ['address' => $phone, 'type' => 'phone'];
                }
                break;
        }
    }

    /**
     * @throws \ReflectionException
     * @throws ExceptionInterface
     */
    private function dispatchNotifications(NotificationTemplate $template, array $recipients, ?Complaint $complaint, object $message): void
    {
        $context = ['complaint' => $complaint, 'message' => $message];

        $renderedSubject = $template->getSubject()
            ? $this->templateRenderer->render($template->getSubject(), (object)$context)
            : null;

        $renderedContent = $this->templateRenderer->render($template->getContent(), (object)$context);

        foreach ($recipients as $recipientInfo) {
            $notificationEntity = $this->notificationFactory->createFromTemplate(
                $template,
                $recipientInfo['address'],
                $recipientInfo['type'],
                [
                    'subject' => $renderedSubject,
                    'content' => $renderedContent,
                    'original_event' => get_class($message)
                ],
                $template->getSentVia() ?: Notification::SENT_VIA_SYSTEM
            );

            if ($renderedSubject) {
                $notificationEntity->setSubject($renderedSubject);
            }

            $this->messageBus->dispatch(new SendNotificationMessage($notificationEntity));

            $this->logger->info(sprintf(
                'Dispatched SendNotificationMessage for template "%s" to %s.',
                $template->getName(),
                $recipientInfo['address']
            ));
        }
    }
}
