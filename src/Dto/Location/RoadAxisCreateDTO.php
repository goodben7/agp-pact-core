<?php

namespace App\Dto\Location;

use phpDocumentor\Reflection\Location;

class RoadAxisCreateDTO
{
    public ?string $name = null;

    public ?string $description = null;

    public ?bool $isActive = true;

    public ?Location $startLocation = null;

    public ?Location $endLocation = null;

    public ?array $traversedLocationIds = [];
}
