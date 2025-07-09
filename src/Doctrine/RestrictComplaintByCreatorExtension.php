<?php

namespace App\Doctrine;

use Doctrine\ORM\QueryBuilder;
use App\Entity\Complaint;
use App\Model\UserProxyInterface;
use ApiPlatform\Metadata\Operation;
use Symfony\Bundle\SecurityBundle\Security;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use App\Entity\User;

class RestrictComplaintByCreatorExtension implements QueryCollectionExtensionInterface
{
    public function __construct(private Security $security)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->restrict($queryBuilder, $resourceClass);
    }

    private function restrict(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (Complaint::class !== $resourceClass)
            return;

        /**
         * @var User $user
         */
        $user = $this->security->getUser();
        if (!$user)
            return;

        if (UserProxyInterface::PERSON_LAMBDA === $user->getPersonType() || UserProxyInterface::PERSON_COMPLAINANT === $user->getPersonType()) {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere(sprintf('%s.createdBy = :currentUser', $rootAlias));
            $queryBuilder->setParameter('currentUser', $user->getId());
        }
    }
}
