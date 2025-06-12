<?php

namespace App\State\Location;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Location\LocationCreateDTO;
use App\Manager\LocationManager;

readonly class LocationCreateProcessor implements ProcessorInterface
{
    public function __construct(private LocationManager $manager)
    {
    }

    /**
     * @param LocationCreateDTO $data
     */
    public function process($data, Operation $operation, $uriVariables = [], $context = [])
    {
        return $this->manager->create($data);
    }
}
