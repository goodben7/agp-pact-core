<?php

namespace App\Manager;

use App\Entity\Complainant;
use App\Model\NewComplainantModel;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
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
            $this->em->persist($complainant);
            $this->em->flush();

            $this->logger->info('Complainant created successfully');

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
