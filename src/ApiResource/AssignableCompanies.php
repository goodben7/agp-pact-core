<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Provider\AssignableCompaniesProvider;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/complaints/{complaintId}/assignable-companies',
            normalizationContext: ['groups' => ['company:get']],
            provider: AssignableCompaniesProvider::class,
            security: "is_granted('ROLE_COMPLAINT_DETAILS')"
        ),
    ],
    formats: ['json' => ['application/json']],
)]
#[ApiFilter(SearchFilter::class, properties: [
    'workflowStepId' => 'exact',
    'location' => 'exact',
    'roadAxis' => 'exact'
])]
final class AssignableCompanies {}
