<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\PrejudiceCreateDTO;
use App\Manager\PrejudiceManager;

readonly class CreatePrejudiceProcessor implements ProcessorInterface
{
    public function __construct(private PrejudiceManager $manager)
    {
    }

    /**
     * @param PrejudiceCreateDTO $data
    */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->manager->create($data);
    }
}
