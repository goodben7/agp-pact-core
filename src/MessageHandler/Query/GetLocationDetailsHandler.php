<?php

namespace App\MessageHandler\Query;

use App\Entity\Location;
use App\Message\Query\GetLocationDetails;
use App\Message\Query\QueryHandlerInterface;
use App\Repository\LocationRepository;

class GetLocationDetailsHandler implements QueryHandlerInterface
{
    public function __construct(private LocationRepository $locationRepository)
    {
    }

    public function __invoke(GetLocationDetails $query): ?Location
    {
        if ($query->id !== null) {
            
            /** @var Location|null $location */
            $location = $this->locationRepository->find($query->id);
            
        } else {
            return null;
        }

        return $location;
    }
}
