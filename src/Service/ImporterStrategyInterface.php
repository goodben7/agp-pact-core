<?php

namespace App\Service;

use App\Entity\ImportItem;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(tags: ['app.importer_strategy'])]
interface ImporterStrategyInterface
{
    /**
     * @throws \Exception
     */
    public function process(ImportItem $item): void;

    public function supports(string $entityType): bool;
}