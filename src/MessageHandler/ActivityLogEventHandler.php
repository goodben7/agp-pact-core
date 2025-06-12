<?php

namespace App\MessageHandler;

use App\Entity\ActivityLog;
use App\Message\ActivityLogEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
readonly class ActivityLogEventHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface        $logger,
    )
    {
    }

    public function __invoke(ActivityLogEvent $event): void
    {
        try {
            $activityLog = (new ActivityLog())
                ->setTimestamp(new \DateTimeImmutable())
                ->setUserId($event->getUserId())
                ->setActivityType($event->getActivityType())
                ->setEntityType($event->getEntityType())
                ->setEntityId($event->getEntityId())
                ->setDescription($event->getDescription())
                ->setDetails($event->getDetails());

            $this->em->persist($activityLog);
            $this->em->flush();

            $this->logger->info('Activity log created', [
                'activity_type' => $event->getActivityType(),
                'entity_type' => $event->getEntityType(),
                'entity_id' => $event->getEntityId(),
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to create activity log', [
                'error' => $e->getMessage(),
                'activity_type' => $event->getActivityType(),
            ]);
        }
    }
}