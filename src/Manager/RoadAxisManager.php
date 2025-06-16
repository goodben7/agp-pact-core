<?php

namespace App\Manager;


use App\Entity\Location;
use App\Entity\RoadAxis;
use App\Model\UpdateRoadAxisModel;
use App\Dto\Location\RoadAxisCreateDTO;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UnavailableDataException;
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

    public function updateFrom(UpdateRoadAxisModel $model, string $roadAxisId): RoadAxis
    {
        $r = $this->findDelivery($roadAxisId);

        $r->setName($model->name);
        $r->setDescription($model->description);
        $r->setStartLocation($model->startLocation);
        $r->setEndLocation($model->endLocation);
        $r->setActive($model->active);

        foreach ($model->traversedLocationIds as $traversedLocationId) {
            $traversedLocation = $this->em->getRepository(Location::class)->find($traversedLocationId);
            if ($traversedLocation)
                $r->addTraversedLocation($traversedLocation);
        }

        $this->em->flush();
        return $r;
    }

    private function findDelivery(string $roadAxisId): RoadAxis 
    {
        $roadAxis = $this->em->find(RoadAxis::class, $roadAxisId);

        if (null === $roadAxis) {
            throw new UnavailableDataException(sprintf('cannot find RoadAxis with id: %s', $roadAxisId));
        }

        return $roadAxis; 
    }
}
