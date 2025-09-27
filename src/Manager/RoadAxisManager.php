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
            ->setActive($data->active);

        foreach ($data->traversedLocationIds as $traversedLocationId) {
            $traversedLocation = $this->em->getRepository(Location::class)->find($traversedLocationId);
            if ($traversedLocation)
                $roadAxis->addTraversedLocation($traversedLocation);
        }
        
        foreach ($data->provinceIds as $provinceId) {
            $province = $this->em->getRepository(Location::class)->find($provinceId);
            if ($province)
                $roadAxis->addProvince($province);
        }
        
        foreach ($data->territoryIds as $territoryId) {
            $territory = $this->em->getRepository(Location::class)->find($territoryId);
            if ($territory)
                $roadAxis->addTerritory($territory);
        }
        
        foreach ($data->communeIds as $communeId) {
            $commune = $this->em->getRepository(Location::class)->find($communeId);
            if ($commune)
                $roadAxis->addCommune($commune);
        }
        
        foreach ($data->quartierId as $quartierId) {
            $quartier = $this->em->getRepository(Location::class)->find($quartierId);
            if ($quartier)
                $roadAxis->addQuartier($quartier);
        }
        
        foreach ($data->cityIds as $cityId) {
            $city = $this->em->getRepository(Location::class)->find($cityId);
            if ($city)
                $roadAxis->addCity($city);
        }
        
        foreach ($data->secteurIds as $secteurId) {
            $secteur = $this->em->getRepository(Location::class)->find($secteurId);
            if ($secteur)
                $roadAxis->addSecteur($secteur);
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
        $r->setActive($model->active);

        // --- Début de la logique pour gérer l'ajout et la suppression des localisations traversées ---

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

        // --- Gestion des provinces ---
        $currentProvinceIds = $r->getProvince()->map(fn(Location $loc) => $loc->getId())->toArray();
        $provincesToRemoveIds = array_diff($currentProvinceIds, $model->provinceIds);
        $provincesToAddIds = array_diff($model->provinceIds, $currentProvinceIds);

        foreach ($r->getProvince() as $province) {
            if (in_array($province->getId(), $provincesToRemoveIds)) {
                $r->removeProvince($province);
            }
        }

        foreach ($provincesToAddIds as $provinceId) {
            $province = $this->em->getRepository(Location::class)->find($provinceId);
            if ($province) {
                $r->addProvince($province);
            }
        }

        // --- Gestion des territoires ---
        $currentTerritoryIds = $r->getTerritory()->map(fn(Location $loc) => $loc->getId())->toArray();
        $territoriesToRemoveIds = array_diff($currentTerritoryIds, $model->territoryIds);
        $territoriesToAddIds = array_diff($model->territoryIds, $currentTerritoryIds);

        foreach ($r->getTerritory() as $territory) {
            if (in_array($territory->getId(), $territoriesToRemoveIds)) {
                $r->removeTerritory($territory);
            }
        }

        foreach ($territoriesToAddIds as $territoryId) {
            $territory = $this->em->getRepository(Location::class)->find($territoryId);
            if ($territory) {
                $r->addTerritory($territory);
            }
        }

        // --- Gestion des communes ---
        $currentCommuneIds = $r->getCommune()->map(fn(Location $loc) => $loc->getId())->toArray();
        $communesToRemoveIds = array_diff($currentCommuneIds, $model->communeIds);
        $communesToAddIds = array_diff($model->communeIds, $currentCommuneIds);

        foreach ($r->getCommune() as $commune) {
            if (in_array($commune->getId(), $communesToRemoveIds)) {
                $r->removeCommune($commune);
            }
        }

        foreach ($communesToAddIds as $communeId) {
            $commune = $this->em->getRepository(Location::class)->find($communeId);
            if ($commune) {
                $r->addCommune($commune);
            }
        }

        // --- Gestion des quartiers ---
        $currentQuartierIds = $r->getQuartier()->map(fn(Location $loc) => $loc->getId())->toArray();
        $quartiersToRemoveIds = array_diff($currentQuartierIds, $model->quartierId);
        $quartiersToAddIds = array_diff($model->quartierId, $currentQuartierIds);

        foreach ($r->getQuartier() as $quartier) {
            if (in_array($quartier->getId(), $quartiersToRemoveIds)) {
                $r->removeQuartier($quartier);
            }
        }

        foreach ($quartiersToAddIds as $quartierId) {
            $quartier = $this->em->getRepository(Location::class)->find($quartierId);
            if ($quartier) {
                $r->addQuartier($quartier);
            }
        }

        // --- Gestion des villes ---
        $currentCityIds = $r->getCity()->map(fn(Location $loc) => $loc->getId())->toArray();
        $citiesToRemoveIds = array_diff($currentCityIds, $model->cityIds);
        $citiesToAddIds = array_diff($model->cityIds, $currentCityIds);

        foreach ($r->getCity() as $city) {
            if (in_array($city->getId(), $citiesToRemoveIds)) {
                $r->removeCity($city);
            }
        }

        foreach ($citiesToAddIds as $cityId) {
            $city = $this->em->getRepository(Location::class)->find($cityId);
            if ($city) {
                $r->addCity($city);
            }
        }

        // --- Gestion des secteurs ---
        $currentSecteurIds = $r->getSecteur()->map(fn(Location $loc) => $loc->getId())->toArray();
        $secteursToRemoveIds = array_diff($currentSecteurIds, $model->secteurIds);
        $secteursToAddIds = array_diff($model->secteurIds, $currentSecteurIds);

        foreach ($r->getSecteur() as $secteur) {
            if (in_array($secteur->getId(), $secteursToRemoveIds)) {
                $r->removeSecteur($secteur);
            }
        }

        foreach ($secteursToAddIds as $secteurId) {
            $secteur = $this->em->getRepository(Location::class)->find($secteurId);
            if ($secteur) {
                $r->addSecteur($secteur);
            }
        }

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
