<?php

namespace App\Model;

use App\Entity\User; 
use App\Entity\Location;
use Symfony\Component\Validator\Constraints as Assert;

class NewComplainantModel
{
    public function __construct(
        public ?string $lastName = null,
        public ?string $middleName = null,
        public ?string $firstName = null,
        public ?string $contactPhone = null,
        public ?string $contactEmail = null,
        public ?string $plainPassword = null, 

        #[Assert\Choice(callback: [User::class, 'getPersonTypesAsList'])]
        public ?string $personType = null, 

        public ?string $address = null,
        public ?Location $province = null,
        public ?Location $territory = null,
        public ?Location $commune = null,
        public ?Location $quartier = null,
        public ?Location $city = null,
        public ?Location $village = null,
    ) {
    }
}