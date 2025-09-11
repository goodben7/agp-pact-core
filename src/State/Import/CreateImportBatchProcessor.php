<?php

namespace App\State\Import;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Import\CreateImportBatchDto;
use App\Entity\ImportBatch;
use App\Manager\ImportBatchManager;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

readonly class CreateImportBatchProcessor implements ProcessorInterface
{
    public function __construct(
        private ImportBatchManager $manager
    )
    {
    }

    /**
     * @param CreateImportBatchDto $data
     * @throws ExceptionInterface
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ImportBatch
    {
        return $this->manager->create($data);
    }
}