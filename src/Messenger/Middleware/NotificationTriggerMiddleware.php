<?php

namespace App\Messenger\Middleware;

use App\Entity\Complaint;
use App\Entity\Notification;
use App\Entity\NotificationTemplate;
use App\Message\SendNotificationMessage;
use App\Factory\NotificationFactory;
use App\Repository\NotificationTemplateRepository;
use App\Service\StringTemplateRenderer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Psr\Log\LoggerInterface;

class NotificationTriggerMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly NotificationTemplateRepository $notificationTemplateRepository,
        private readonly NotificationFactory            $notificationFactory,
        private readonly MessageBusInterface            $messageBus,
        private readonly StringTemplateRenderer         $templateRenderer,
        private readonly EntityManagerInterface         $entityManager,
        private readonly LoggerInterface                $logger
    )
    {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $envelope = $stack->next()->handle($envelope, $stack);

        $message = $envelope->getMessage();
        $messageFqcn = get_class($message);

        if ($envelope->last(HandledStamp::class)) {
            $this->logger->debug(sprintf('Middleware processing message: %s', $messageFqcn));

            $templates = $this->notificationTemplateRepository->findBy([
                'triggerEvent' => $messageFqcn,
                'active' => true
            ]);

            if (empty($templates)) {
                $this->logger->debug(sprintf('No active notification templates found for event: %s', $messageFqcn));
                return $envelope;
            }

            $complaint = null;
            if (method_exists($message, 'getComplaintId')) {
                $complaintId = $message->getComplaintId();
                $complaint = $this->entityManager->getRepository(Complaint::class)->find($complaintId);

                if (!$complaint) {
                    $this->logger->warning(sprintf('Complaint %s not found for notification triggering, skipping.', $complaintId));
                    return $envelope;
                }

                $complaint->getComplainant();
            }

            foreach ($templates as $template) {
                try {
                    $recipient = null;
                    $recipientType = null;

                    if ($complaint && $template->getSentVia() === Notification::RECIPIENT_TYPE_EMAIL && $complaint->getComplainant()->getContactEmail()) {
                        $recipient = $complaint->getComplainant()->getContactEmail();
                        $recipientType = 'email';
                    } elseif ($complaint && $template->getSentVia() === Notification::SENT_VIA_SMS && $complaint->getComplainant()->getContactPhone()) {
                        $recipient = $complaint->getComplainant()->getContactPhone();
                        $recipientType = 'phone';
                    }

                    if (!$recipient || !$recipientType) {
                        $this->logger->warning(sprintf(
                            'Could not determine recipient for template "%s" (type: %s) triggered by %s.',
                            $template->getName(),
                            $template->getSentVia(),
                            $messageFqcn
                        ));
                        continue;
                    }

                    $renderedSubject = $template->getSubject()
                        ? $this->templateRenderer->render($template->getSubject(), $complaint ?? $message)
                        : null;

                    $renderedContent = $this->templateRenderer->render($template->getContent(), $complaint ?? $message);

                    $notificationEntity = $this->notificationFactory->createFromTemplate(
                        $template,
                        $recipient,
                        $recipientType,
                        [
                            'subject' => $renderedSubject,
                            'content' => $renderedContent,
                            'original_event' => $messageFqcn
                        ],
                        $template->getSentVia() ?: Notification::SENT_VIA_SYSTEM
                    );

                    if ($renderedSubject) {
                        $notificationEntity->setSubject($renderedSubject);
                    }

                    $this->messageBus->dispatch(new SendNotificationMessage($notificationEntity));

                    $this->logger->info(sprintf(
                        'Dispatched SendNotificationMessage for template "%s" (ID: %s) triggered by %s for recipient %s.',
                        $template->getName(),
                        $template->getId(),
                        $messageFqcn,
                        $recipient
                    ));

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
        }

        return $envelope;
    }
}
