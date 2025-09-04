<?php

namespace App\Dto;

use App\Entity\User;
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
        #[Assert\Choice(callback: [User::class, 'getAcceptedPersonList'])]
        public ?string $personType = null,

    ) {}
}
