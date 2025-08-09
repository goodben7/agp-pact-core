<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Dto\Company\CompanyStatisticsDto;
use App\Provider\CompanyStatisticsProvider;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/company_statistics',
            normalizationContext: ['groups' => ['company_stats:read']],
            output: CompanyStatisticsDto::class,
            provider: CompanyStatisticsProvider::class,
        ),
    ],
    formats: ['json' => ['application/json']],
)]
#[ApiFilter(SearchFilter::class, properties: [
    'location' => 'exact',
    'involvedCompany' => 'exact',
    'roadAxisId' => 'exact',
    'complaintTypeId' => 'exact',
    'workflowStepId' => 'exact'
])]
#[ApiFilter(DateFilter::class, properties: ['declarationDate'])]
final class CompanyStatistics
{
}
