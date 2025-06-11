<?php

namespace App\Manager;

use App\Dto\Location\LocationCreateDTO;
use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;

readonly class LocationManager
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    public function create(LocationCreateDTO $data): Location
    {
        $location = (new Location())
            ->setName($data->name)
            ->setLevel($data->level)
            ->setParent($data->parent)
            ->setCode($data->code)
            ->setActive($data->active);

        $this->em->persist($location);
        $this->em->flush();

        return $location;
    }
}
