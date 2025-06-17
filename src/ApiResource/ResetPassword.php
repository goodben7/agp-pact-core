<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Dto\Auth\ResetPasswordDto;
use App\State\Auth\ResetPasswordProcessor;
use App\Provider\Auth\ResetPasswordProvider;

#[ApiResource(
    shortName: 'ResetPassword',
    operations: [
        new Post(
            uriTemplate: '/auth/reset-password',
            input: ResetPasswordDto::class,
            output: false,
            provider: ResetPasswordProvider::class,
            processor: ResetPasswordProcessor::class
        )
    ]
)]
class ResetPassword
{
    public string $message = 'Password has been reset successfully';
}
