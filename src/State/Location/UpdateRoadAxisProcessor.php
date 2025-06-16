<?php

namespace App\State\Location;

use App\Entity\RoadAxis;
use App\Manager\RoadAxisManager;
use App\Model\UpdateRoadAxisModel;
use ApiPlatform\Metadata\Operation;
use App\Dto\Location\RoadAxisUpdateDTO;
use ApiPlatform\State\ProcessorInterface;

readonly class UpdateRoadAxisProcessor implements ProcessorInterface
{
    public function __construct(private RoadAxisManager $manager)
    {
    }

    /**
     * @param RoadAxisUpdateDTO $data
     */
    public function process($data, Operation $operation, $uriVariables = [], $context = []): RoadAxis
    {
        $model = new UpdateRoadAxisModel( 
            $data->name,
            $data->description,
            $data->active,
            $data->startLocation,
            $data->endLocation
        );
 
        return $this->manager->updateFrom($model, $uriVariables['id']); 
    }
}


