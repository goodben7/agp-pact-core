<?php

namespace App\Dto\Location;

class LocationUpdateDTO
{
    public ?string $name = null;
    public ?string $level = null;
    public ?string $parentId = null;
    public ?string $code = null;
    public ?bool $isActive = null;
}
