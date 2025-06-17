<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Dto\Report\RequestReportDto;
use App\Provider\Report\RequestReportProvider;
use App\State\Report\RequestReportProcessor;

#[ApiResource(
    shortName: "RequestReport",
    operations: [
        new Post(
            uriTemplate: '/reports/request',
            input: RequestReportDto::class,
            output: false,
            provider: RequestReportProvider::class,
            processor: RequestReportProcessor::class
        )
    ]
)]
class RequestReportResource
{
    public string $message = "Report request has been sent successfully";
}
