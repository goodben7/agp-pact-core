<?php

namespace App\Dto\Auth;

readonly class ForgotPasswordDto
{
    public function __construct(
        public ?string $identifier = null,
        public ?string $identifierType = null,
    )
    {
    }
}
