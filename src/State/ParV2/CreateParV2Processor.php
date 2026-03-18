<?php

namespace App\State\ParV2;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\ParV2\CreateParV2Dto;
use App\Entity\ParV2;
use App\Manager\ParV2Manager;
use App\Model\NewParV2Model;

final class CreateParV2Processor implements ProcessorInterface
{
    public function __construct(private ParV2Manager $manager)
    {
    }

    /**
     * @param CreateParV2Dto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ParV2
    {
        $model = NewParV2Model::fromKoboPayload($data->rawPayload, $data->koboId);

        return $this->manager->create($model);
    }
}

