<?php

namespace App\Dto\Complainant;

use App\Entity\Location;

class ComplainantCreateDTO
{
    public ?string $lastName = null;

    public ?string $middleName = null;

    public ?string $firstName = null;

    public ?string $contactPhone = null;

    public ?string $contactEmail = null;

    public ?string $personType = null;

    public ?string $address = null;

    public ?Location $province = null;

    public ?Location $territory = null;

    public ?Location $commune = null;

    public ?Location $quartier = null;

    public ?Location $city = null;

    public ?Location $village = null;

}
