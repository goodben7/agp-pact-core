<?php

namespace App\Factory;

use App\Service\ImporterStrategyInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

readonly class ImporterStrategyFactory
{
    private iterable $strategies;

    /**
     * @param iterable<ImporterStrategyInterface> $strategies
     */
    public function __construct(
        #[AutowireIterator('app.importer_strategy')] iterable $strategies
    )
    {
        $this->strategies = $strategies;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function getStrategy(string $entityType): ImporterStrategyInterface
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($entityType)) {
                return $strategy;
            }
        }

        throw new \InvalidArgumentException(sprintf("Aucune stratégie d'importation n'a été trouvée pour le type d'entité %s.", $entityType));
    }
}