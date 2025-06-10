<?php

namespace App\Dto\Location;

class RoadAxisCreateDTO
{
    public ?string $name = null;
    public ?string $description = null;
    public ?bool $isActive = true;
    public ?string $startLocationId = null;
    public ?string $endLocationId = null;
    public ?array $traversedLocationIds = [];
}
