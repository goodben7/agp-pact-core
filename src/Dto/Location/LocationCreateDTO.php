<?php

namespace App\Dto\Location;

class LocationCreateDTO
{
    public ?string $name = null;
    public ?string $level = null;
    public ?string $parentId = null;
    public ?string $code = null;
    public ?bool $isActive = true;
}
