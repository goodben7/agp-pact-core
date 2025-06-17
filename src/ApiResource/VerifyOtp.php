<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Dto\Auth\VerifyOtpDto;
use App\State\Auth\VerifyOtpProcessor;
use App\Provider\Auth\VerifyOtpProvider;

#[ApiResource(
    shortName: 'VerifyOtp',
    operations: [
        new Post(
            uriTemplate: '/auth/verify-otp',
            input: VerifyOtpDto::class,
            output: false,
            provider: VerifyOtpProvider::class,
            processor: VerifyOtpProcessor::class
        )
    ]
)]
class VerifyOtp
{
    public string $message = 'OTP verification successful';
    public bool $verified = false;
}
