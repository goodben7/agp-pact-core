<?php

namespace App\Dto\Location;


class SpeciesPriceUpdateDTO
{
    public ?string $speciesTypeId = null;
    public ?string $roadAxisId = null;
    public ?float $pricePerUnit = null;
    public ?string $unitId = null;
    public ?string $currencyId = null;
    public ?string $effectiveDate = null;
    public ?string $expirationDate = null;
    public ?bool $isActive = null;
}
