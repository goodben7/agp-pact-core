<?php

namespace App\Manager;


use App\Dto\Location\RoadAxisCreateDTO;
use App\Entity\Location;
use App\Entity\RoadAxis;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class RoadAxisManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface    $bus
    )
    {
    }

    public function create(RoadAxisCreateDTO $data): RoadAxis
    {
        $roadAxis = (new RoadAxis())
            ->setName($data->name)
            ->setDescription($data->description)
            ->setStartLocation($data->startLocation)
            ->setEndLocation($data->endLocation)
            ->setActive($data->active);

        foreach ($data->traversedLocationIds as $traversedLocationId) {
            $traversedLocation = $this->em->getRepository(Location::class)->find($traversedLocationId);
            if ($traversedLocation)
                $roadAxis->addTraversedLocation($traversedLocation);
        }

        $this->em->persist($roadAxis);
        $this->em->flush();

        return $roadAxis;
    }
}
