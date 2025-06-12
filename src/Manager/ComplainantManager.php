<?php

namespace App\Manager;


use App\Dto\Complainant\ComplainantCreateDTO;
use App\Entity\Complainant;
use Doctrine\ORM\EntityManagerInterface;

readonly class ComplainantManager
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    public function create(ComplainantCreateDTO $data): Complainant
    {
        $complainant = (new Complainant())
            ->setFirstName($data->firstName)
            ->setMiddleName($data->middleName)
            ->setLastName($data->lastName)
            ->setPersonType($data->personType)
            ->setContactPhone($data->contactPhone)
            ->setContactEmail($data->contactEmail)
            ->setAddress($data->address)
            ->setCity($data->city)
            ->setCommune($data->commune)
            ->setVillage($data->village)
            ->setQuartier($data->quartier)
            ->setTerritory($data->territory)
            ->setProvince($data->province);

        $this->em->persist($complainant);
        $this->em->flush();

        return $complainant;
    }
}
