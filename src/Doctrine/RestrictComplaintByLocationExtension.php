<?php

namespace App\Doctrine;

use Doctrine\ORM\QueryBuilder;
use App\Entity\Complaint;
use App\Model\UserProxyInterface;
use ApiPlatform\Metadata\Operation;
use Symfony\Bundle\SecurityBundle\Security;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use App\Entity\Member;
use App\Entity\User;
use App\Repository\MemberRepository;

class RestrictComplaintByLocationExtension implements QueryCollectionExtensionInterface
{
    public function __construct(
        private Security $security,
        private MemberRepository $memberRepository
    ) {
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

        // Admins and super-admins see all complaints
        if (
            in_array($user->getPersonType(), [
                UserProxyInterface::PERSON_ADMIN,
                UserProxyInterface::PERSON_SUPER_ADMIN
            ])
        ) {
            return;
        }

        if (UserProxyInterface::PERSON_COMMITTEE === $user->getPersonType()) {
            /**
             * @var Member $member
             */
            $member = $this->memberRepository->findOneBy(['userId' => $user->getId()]);

            if ($member && $member->getCompany()) {
                $rootAlias = $queryBuilder->getRootAliases()[0];
                $companyId = $member->getCompany()->getId();
                // Committee members only see complaints assigned to their company
                $queryBuilder->andWhere(sprintf('%s.involvedCompany = :committeeCompanyId', $rootAlias));
                $queryBuilder->setParameter('committeeCompanyId', $companyId);
            }
        }
    }
}
