<?php

namespace App\Manager;


use App\Dto\RequireAccessDto;
use App\Repository\MemberRepository;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Message\Command\CreateUserCommand;
use App\Exception\UnavailableDataException;
use App\Entity\Member;
use App\Entity\User;
use App\Message\Command\CommandBusInterface;

readonly class MemberManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private MemberRepository $repository,
        private ProfileRepository $profileRepository,
        private CommandBusInterface $bus
    )
    {
    }

    public function setAccess(RequireAccessDto $data, string $memberId)
    {
        try {
            /**
             * @var Member $member
             */
            $member = $this->repository->findOneBy(['id' => $memberId]);

            if (null === $member) {
                throw new UnavailableDataException(sprintf('cannot find member with id: %s', $memberId));
            }
            
            if (null !== $member->getUserId()) {
                throw new UnavailableDataException(sprintf('Member with id: %s already has a user account', $memberId));
            }
            
            $profile = $this->profileRepository->findOneBy(['personType' => $data->personType]);
            
            if (null === $profile) {
                throw new UnavailableDataException(sprintf('Cannot find profile with personType: %s', $data->personType));
            }

            $command = new CreateUserCommand(
                $data->plainPassword,
                $profile,
                $data->email,
                $data->phone,
                $data->displayName     
            );

            /**
             * @var User $u
             */
            $u = $this->bus->dispatch($command);

            $member->setUserId($u->getId());
            $member->setEmail($u->getEmail());
            $member->setPhone($u->getPhone());
            $member->setDisplayName($u->getDisplayName());
            
            $this->em->persist($member);
            $this->em->flush();

            return $member; 
            
        } catch (\Exception $e) {
            throw new UnavailableDataException(sprintf('Error while setting access for member: %s. Error: %s', $memberId, $e->getMessage()));
        }
    }

}