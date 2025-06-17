<?php

namespace App\Manager;

use App\Entity\Complainant;
use App\Model\UserProxyInterface;
use App\Model\NewComplainantModel;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Message\Command\CreateUserCommand;
use App\Exception\UnavailableDataException;
use App\Message\Command\CommandBusInterface;
use Psr\Log\LoggerInterface;
use App\Exception\ComplainantCreationException;

readonly class ComplainantManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private ProfileRepository $profileRepository,
        private CommandBusInterface $bus,
        private ?LoggerInterface $logger = null,
    )
    {
    }

    /**
     * Creates a new Complainant entity from the provided DTO.
     * @param NewComplainantModel $model
     * @return Complainant The newly created Complainant entity.
     */
    public function create(NewComplainantModel $model): Complainant
    {
        $complainant = (new Complainant())
            ->setFirstName($model->firstName)
            ->setMiddleName($model->middleName)
            ->setLastName($model->lastName)
            ->setPersonType($model->personType)
            ->setContactPhone($model->contactPhone)
            ->setContactEmail($model->contactEmail)
            ->setAddress($model->address)
            ->setCity($model->city)
            ->setCommune($model->commune)
            ->setVillage($model->village)
            ->setQuartier($model->quartier)
            ->setTerritory($model->territory)
            ->setProvince($model->province)
            ->setSecteur($model->secteur)
            ->setOrganizationStatus($model->organizationStatus)
            ->setLegalPersonality($model->legalPersonality)
        ;

        try {

            $profile = $this->profileRepository->findOneBy(['personType' => UserProxyInterface::PERSON_COMPLAINANT]);

            if (null === $profile) {
                throw new UnavailableDataException('cannot find profile');
            }


            $this->em->persist($complainant);
            $this->em->flush();

            $user = $this->bus->dispatch(
                new CreateUserCommand(
                    $model->plainPassword,
                    $profile,
                    $complainant->getContactEmail(),
                    $complainant->getContactPhone(),
                    $complainant->getFirstName(),
                )
            );

            $this->logger->info('User created successfully');

            $complainant->setUserId($user->getId());

            $this->logger->info('Complainant created successfully');

            $this->em->persist($complainant);
            $this->em->flush();

            return $complainant;
        } catch (\Exception $e) {
            $this->logger?->error(
                'Failed to create complainant: {message}',
                ['message' => $e->getMessage(), 'exception' => $e]
            );
            throw new ComplainantCreationException("An error occurred while creating the complainant. : ". $e->getMessage());
        }
    }
}
