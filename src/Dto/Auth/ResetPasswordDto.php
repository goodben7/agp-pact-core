<?php

namespace App\Dto\Auth;

readonly class ResetPasswordDto
{
    public function __construct(
        public string $identifier,
        public string $identifierType,
        public string $newPassword
    )
    {
    }
}
