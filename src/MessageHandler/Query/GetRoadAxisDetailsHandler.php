<?php

namespace App\MessageHandler\Query;

use App\Entity\RoadAxis;
use App\Message\Query\GetRoadAxisDetails;
use App\Message\Query\QueryHandlerInterface;
use App\Repository\RoadAxisRepository;

class GetRoadAxisDetailsHandler implements QueryHandlerInterface
{
    public function __construct(private RoadAxisRepository $roadAxisRepository)
    {
    }

    public function __invoke(GetRoadAxisDetails $query): ?RoadAxis
    {
        if ($query->id !== null) {
            
            /** @var RoadAxis|null $roadAxis */
            $roadAxis = $this->roadAxisRepository->find($query->id);
            
        } else {
            return null;
        }

        return $roadAxis;
    }
}
