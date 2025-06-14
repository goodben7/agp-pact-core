<?php

namespace App\Service;

use App\Entity\User;
use App\Message\ActivityLogEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class ActivityLogDispatcher
{
    public function __construct(
        private MessageBusInterface $bus,
        private Security $security,
        private LoggerInterface     $logger
    )
    {
    }

    public function dispatch(string $action, mixed $resource, ?string $resourceClass = null): void
    {
        try {
            $entityType = $this->getEntityType($resource, $resourceClass);
            $entityId = $this->getEntityId($resource);

            /** @var User */
            $user = $this->security->getUser();

            $this->bus->dispatch(new ActivityLogEvent(
                activityType: sprintf('%s.%s', $entityType, $action),
                entityType: $entityType,
                entityId: $entityId,
                description: $this->generateDescription($action, $entityType),
                details: $this->generateDetails($action, $resource, $resourceClass),
                userId: $user?->getId()
            ));
        } catch (\Throwable $e) {
            $this->logger->error('Failed to dispatch activity log event', [
                'action' => $action,
                'resource_class' => $resourceClass ?? (is_object($resource) ? get_class($resource) : gettype($resource)),
                'error' => $e->getMessage(),
                'exception' => $e
            ]);
        }
    }

    private function getEntityType(mixed $resource, ?string $resourceClass): string
    {
        if ($resourceClass) {
            return strtolower(basename(str_replace('\\', '/', $resourceClass)));
        }

        return strtolower(basename(str_replace('\\', '/', get_class($resource))));
    }

    private function getEntityId(mixed $resource): ?string
    {
        if (is_object($resource) && method_exists($resource, 'getId')) {
            return (string)$resource->getId();
        }

        return null;
    }

    private function generateDescription(string $action, string $entityType): string
    {
        return match ($action) {
            'get' => sprintf('%s retrieved', ucfirst($entityType)),
            'list' => sprintf('%s collection retrieved', ucfirst($entityType)),
            'create' => sprintf('%s created', ucfirst($entityType)),
            'update' => sprintf('%s updated', ucfirst($entityType)),
            'delete' => sprintf('%s deleted', ucfirst($entityType)),
            default => sprintf('%s %s', ucfirst($entityType), $action)
        };
    }

    private function generateDetails(string $action, mixed $resource, ?string $resourceClass): array
    {
        $details = [
            'action' => $action,
            'entity_class' => $resourceClass ?? get_class($resource)
        ];

        if ($action === 'list' && is_array($resource)) {
            $details['count'] = count($resource);
        }

        return $details;
    }
}