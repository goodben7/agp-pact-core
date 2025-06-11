<?php

namespace App\Dto\Location;

use App\Entity\Location;

class LocationCreateDTO
{
    public ?string $name = null;

    public ?string $level = null;

    public ?Location $parent = null;

    public ?string $code = null;

    public ?bool $active = true;
}
