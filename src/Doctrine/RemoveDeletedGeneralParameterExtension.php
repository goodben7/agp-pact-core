<?php

namespace App\Doctrine;

use Doctrine\ORM\QueryBuilder;
use App\Entity\GeneralParameter;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;

class RemoveDeletedGeneralParameterExtension implements QueryCollectionExtensionInterface
{
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->restrict($queryBuilder, $resourceClass);
    }

    private function restrict(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (GeneralParameter::class !== $resourceClass) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.deleted != :isDeleted', $rootAlias));
        $queryBuilder->setParameter('isDeleted', true);
    }
}