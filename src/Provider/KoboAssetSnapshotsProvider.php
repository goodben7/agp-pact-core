<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Kobo\KoboAssetSnapshotDto;
use App\Manager\ParV2Manager;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class KoboAssetSnapshotsProvider implements ProviderInterface
{
    public function __construct(
        private ParV2Manager $manager,
        private RequestStack $requestStack
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $includeStructure = false;
        if ($request) {
            $includeStructure = filter_var($request->query->get('includeStructure', false), FILTER_VALIDATE_BOOLEAN);
        }

        $payload = $this->manager->getKoboAssetSnapshots();
        $results = $payload['results'] ?? [];
        if (!is_array($results)) {
            return [];
        }

        $items = [];
        foreach ($results as $snapshot) {
            if (!is_array($snapshot)) {
                continue;
            }

            $dto = new KoboAssetSnapshotDto();
            $dto->snapshotId = isset($snapshot['uid']) && (is_string($snapshot['uid']) || is_int($snapshot['uid'])) ? (string) $snapshot['uid'] : null;
            $dto->assetId = isset($snapshot['asset']) && (is_string($snapshot['asset']) || is_int($snapshot['asset'])) ? $this->manager->normalizeAssetId((string) $snapshot['asset']) : null;
            $dto->assetName = isset($snapshot['name']) && (is_string($snapshot['name']) || is_int($snapshot['name'])) ? (string) $snapshot['name'] : null;

            if ($includeStructure) {
                $structure = $this->manager->getKoboFormStructureFromSnapshot($snapshot);
                $dto->survey = is_array($structure['survey'] ?? null) ? $structure['survey'] : [];
                $dto->choices = is_array($structure['choices'] ?? null) ? $structure['choices'] : [];
            }

            $items[] = $dto;
        }

        return $items;
    }
}
