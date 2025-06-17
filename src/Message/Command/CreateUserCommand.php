<?php

namespace App\Message\Command;

use App\Entity\Profile;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUserCommand implements CommandInterface
{

    public function __construct(

        #[Assert\NotNull]
        #[Assert\NotBlank]
        public ?string  $plainPassword = null,

        #[Assert\NotNull]
        #[Assert\NotBlank]
        public ?Profile $profile = null,

        #[Assert\Email]
        public ?string  $email = null,

        public ?string  $phone = null,

        public ?string  $displayName = null,

    )
    {
    }
}
