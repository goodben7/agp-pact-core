<?php

namespace App\Repository;

use App\Entity\Member;
use App\Entity\Profile;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     *
     * @param string $userId
     * @return string|null
     */
    public function findCompanyIdByUserId(string $userId): ?string
    {
        $entityManager = $this->getEntityManager();

        $memberRepository = $entityManager->getRepository(Member::class);
        $member = $memberRepository->findOneBy(['userId' => $userId]);

        if (!$member) {
            return null;
        }

        $company = $member->getCompany();

        return $company?->getId();
    }

    public function findUsersByProfile(Profile $profile)
    {
        return $this->createQueryBuilder('u')
            ->where('u.profile = :profile')
            ->setParameter('profile', $profile)
            ->getQuery()
            ->getResult();
    }
}
