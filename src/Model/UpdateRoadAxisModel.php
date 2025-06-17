<?php
namespace App\Model;

use App\Entity\Location;


class UpdateRoadAxisModel
{
    public function __construct(
        public ?string $name = null,
        public ?string $description = null,
        public ?bool $active = true,
        public ?Location $startLocation = null,
        public ?Location $endLocation = null,
        public ?array $traversedLocationIds = [],
    )
    {
    }
}
