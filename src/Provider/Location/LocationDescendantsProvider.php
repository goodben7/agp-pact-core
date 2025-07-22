<?php

namespace App\Provider\Location;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\LocationRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class LocationDescendantsProvider implements ProviderInterface
{
    public function __construct(private LocationRepository $locationRepository)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $ancestorId = $uriVariables['id'] ?? null;
        $levelCode = $uriVariables['levelCode'] ?? null;

        if (!$ancestorId || !$levelCode)
            throw new NotFoundHttpException("Ancestor ID and level code are required.");

        $ancestor = $this->locationRepository->find($ancestorId);
        if (!$ancestor)
            throw new NotFoundHttpException(sprintf('Location with ID "%s" not found.', $ancestorId));

        return $this->locationRepository->findDescendantsByLevel($ancestorId, $levelCode);
    }
}
