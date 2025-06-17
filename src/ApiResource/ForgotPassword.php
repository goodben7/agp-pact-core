<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Dto\Auth\ForgotPasswordDto;
use App\State\Auth\ForgotPasswordProcessor;
use App\Provider\Auth\ForgotPasswordProvider;

#[ApiResource(
    shortName: 'ForgotPassword',
    operations: [
        new Post(
            uriTemplate: '/auth/forgot-password',
            input: ForgotPasswordDto::class,
            output: false,
            provider: ForgotPasswordProvider::class,
            processor: ForgotPasswordProcessor::class
        )
    ]
)]
class ForgotPassword
{
    public string $message = 'If your account exists, a verification code has been sent';
}
