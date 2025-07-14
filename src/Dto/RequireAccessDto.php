<?php

namespace App\Dto;

use App\Model\UserProxyInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RequireAccessDto
{
    public function __construct(
        
        #[Assert\Email]
        public ?string $email = null,
        
        #[Assert\NotNull]
        #[Assert\NotBlank]
        public ?string $plainPassword = null, 

        public ?string $phone = null,

        public ?string $displayName = null,

        #[Assert\NotNull]
        #[Assert\NotBlank]
        #[Assert\Choice(choices: [
            UserProxyInterface::PERSON_ADMIN,
            UserProxyInterface::PERSON_COMMITTEE,
            UserProxyInterface::PERSON_NGO,
            UserProxyInterface::PERSON_COMPANY,
            UserProxyInterface::PERSON_CONTROL_MISSION,
            UserProxyInterface::PERSON_INFRASTRUCTURE_CELL,
            UserProxyInterface::PERSON_WORLD_BANK,
            UserProxyInterface::PERSON_COMPLAINANT,
            UserProxyInterface::PERSON_LAMBDA
        ])]
        public ?string $personType = null,

    )
    {  
    }
}