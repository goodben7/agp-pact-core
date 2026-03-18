<?php

namespace App\State\ParV2;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\ParV2\SyncParV2RequestDto;
use App\Dto\ParV2\SyncParV2ResultDto;
use App\Manager\ParV2Manager;

final class SyncParV2Processor implements ProcessorInterface
{
    public function __construct(private ParV2Manager $manager)
    {
    }

    /**
     * @param SyncParV2RequestDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): SyncParV2ResultDto
    {
        if (!$data->assetId) {
            throw new \InvalidArgumentException('assetId is required');
        }

        $result = $this->manager->syncFromKoboAsset(
            $data->assetId,
            $data->limit,
            $data->start,
            $data->maxResults
        );

        $dto = new SyncParV2ResultDto();
        $dto->processed = (int) ($result['processed'] ?? 0);
        $dto->created = (int) ($result['created'] ?? 0);
        $dto->updated = (int) ($result['updated'] ?? 0);

        return $dto;
    }
}

