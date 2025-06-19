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
        $r = $this->findRoadAxis($roadAxisId);

        $r->setName($model->name);
        $r->setDescription($model->description);
        $r->setStartLocation($model->startLocation);
        $r->setEndLocation($model->endLocation);
        $r->setActive($model->active);

        // --- Début de la logique pour gérer l'ajout et la suppression ---

        // 1. Obtenir les IDs des localisations traversées actuellement associées à RoadAxis
        $currentTraversedLocationIds = $r->getTraversedLocations()->map(fn(Location $loc) => $loc->getId())->toArray();

        // 2. Identifier les localisations à supprimer
        // Ce sont celles qui sont dans $currentTraversedLocationIds mais pas dans $model->traversedLocationIds
        $locationsToRemoveIds = array_diff($currentTraversedLocationIds, $model->traversedLocationIds);

        foreach ($r->getTraversedLocations() as $traversedLocation) {
            if (in_array($traversedLocation->getId(), $locationsToRemoveIds)) {
                $r->removeTraversedLocation($traversedLocation);
            }
        }

        // 3. Identifier les localisations à ajouter
        // Ce sont celles qui sont dans $model->traversedLocationIds mais pas dans $currentTraversedLocationIds
        $locationsToAddIds = array_diff($model->traversedLocationIds, $currentTraversedLocationIds);

        foreach ($locationsToAddIds as $traversedLocationId) {
            $traversedLocation = $this->em->getRepository(Location::class)->find($traversedLocationId);
            if ($traversedLocation) {
                $r->addTraversedLocation($traversedLocation);
            }
        }

        // --- Fin de la logique pour gérer l'ajout et la suppression ---

        $this->em->flush();
        return $r;
    }

    private function findRoadAxis(string $roadAxisId): RoadAxis 
    {
        $roadAxis = $this->em->find(RoadAxis::class, $roadAxisId);

        if (null === $roadAxis) {
            throw new UnavailableDataException(sprintf('cannot find RoadAxis with id: %s', $roadAxisId));
        }

        return $roadAxis; 
    }
}
