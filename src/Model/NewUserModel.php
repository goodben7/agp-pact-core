<?php

namespace App\Model;

use App\Entity\Profile;
use App\Entity\RoadAxis;
use Symfony\Component\Validator\Constraints as Assert;

class NewUserModel
{
    public function __construct(
        
        #[Assert\Email]
        public ?string $email = null,
        
        #[Assert\NotNull]
        #[Assert\NotBlank]
        public ?string $plainPassword = null, 

        #[Assert\NotNull]
        #[Assert\NotBlank]
        public ?Profile $profile = null,

        public ?string $phone = null,

        public ?string $displayName = null,

        public ?RoadAxis $roadAxis = null,

    )
    {  
    }
}