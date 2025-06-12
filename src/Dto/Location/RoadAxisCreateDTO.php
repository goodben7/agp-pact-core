<?php

namespace App\Dto\Location;


use App\Entity\Location;

class RoadAxisCreateDTO
{
    public ?string $name = null;

    public ?string $description = null;

    public ?bool $active = true;

    public ?Location $startLocation = null;

    public ?Location $endLocation = null;

    public ?array $traversedLocationIds = [];
}
