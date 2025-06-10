<?php

namespace App\Dto\Location;


class RoadAxisUpdateDTO
{
    public ?string $name = null;
    public ?string $description = null;
    public ?bool $isActive = null;
    public ?string $startLocationId = null;
    public ?string $endLocationId = null;
    public ?array $traversedLocationIds = null;
}
