<?php

namespace App\State\Location;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Location\RoadAxisCreateDTO;
use App\Entity\RoadAxis;
use App\Manager\RoadAxisManager;

readonly class CreateRoadAxisProcessor implements ProcessorInterface
{
    public function __construct(private RoadAxisManager $manager)
    {
    }

    /**
     * @param RoadAxisCreateDTO $data
     */
    public function process($data, Operation $operation, $uriVariables = [], $context = []): RoadAxis
    {
        return $this->manager->create($data);
    }
}
