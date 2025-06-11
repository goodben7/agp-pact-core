<?php

namespace App\State\GeneralParameter;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\GeneralParameter\GeneralParameterCreateDTO;
use App\Entity\GeneralParameter;
use App\Manager\GenerateParameterManager;

readonly class GeneralParameterCreateProcessor implements ProcessorInterface
{
    public function __construct(private GenerateParameterManager $manager)
    {
    }

    /**
     * @param GeneralParameterCreateDTO $data
     */
    public function process($data, Operation $operation, $uriVariables = [], $context = []): GeneralParameter
    {
        return $this->manager->create($data);
    }
}
