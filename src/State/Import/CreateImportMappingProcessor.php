<?php

namespace App\State\Import;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\ImportMapping;
use App\Manager\ImportMappingManager;

readonly class CreateImportMappingProcessor implements ProcessorInterface
{
    public function __construct(private ImportMappingManager $manager)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ImportMapping
    {
        return $this->manager->create($data);
    }
}