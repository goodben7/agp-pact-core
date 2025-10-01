<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Provider\ParStatisticsProvider;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/par_statistics',
            normalizationContext: ['groups' => ['par_stats:read']],
            output: self::class,
            read: true,
            name: 'get_par_statistics',
            provider: ParStatisticsProvider::class,
        ),
    ],
    formats: ['json' => ['application/json']],
)]
#[ApiFilter(SearchFilter::class, properties: [
    'type' => 'exact',
    'province' => 'exact',
    'territory' => 'exact',
    'village' => 'exact',
    'sexe' => 'exact',
    'category' => 'exact',
    'typeLiability' => 'exact',
    'orientation' => 'exact',
    'vulnerabilityType' => 'exact'
])]
#[ApiFilter(BooleanFilter::class, properties: ['vulnerability', 'formerPap'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt'])]
final class ParStatistics
{
    // Statistiques principales
    #[Groups(["par_stats:read"])]
    public int $totalPars = 0;

    #[Groups(["par_stats:read"])]
    public int $tombsPars = 0;

    #[Groups(["par_stats:read"])]
    public int $ownerPars = 0;

    #[Groups(["par_stats:read"])]
    public int $tenantPars = 0;

    #[Groups(["par_stats:read"])]
    public int $minorPars = 0;

    #[Groups(["par_stats:read"])]
    public int $otherPars = 0;

    #[Groups(["par_stats:read"])]
    public array $parsByType = [];

    #[Groups(["par_stats:read"])]
    public array $parsByVulnerability = [];

    #[Groups(["par_stats:read"])]
    public array $parsByProvince = [];

    #[Groups(["par_stats:read"])]
    public array $parsByTerritory = [];

    #[Groups(["par_stats:read"])]
    public array $parsByVillage = [];

    #[Groups(["par_stats:read"])]
    public array $parsCreatedMonthly = [];

    #[Groups(["par_stats:read"])]
    public float $averageAge = 0.0;

    #[Groups(["par_stats:read"])]
    public array $parsByGender = [];

    #[Groups(["par_stats:read"])]
    public int $vulnerablePars = 0;

    #[Groups(["par_stats:read"])]
    public int $formerPapCount = 0;

    #[Groups(["par_stats:read"])]
    public float $totalCompensationAmount = 0.0;

    // Propriétés pour les filtres
    public ?string $type = null;
    public ?string $province = null;
    public ?string $territory = null;
    public ?string $village = null;
    public ?string $sexe = null;
    public ?string $category = null;
    public ?string $typeLiability = null;
    public ?string $orientation = null;
    public ?string $vulnerabilityType = null;
    public ?bool $vulnerability = null;
    public ?bool $formerPap = null;
    public ?\DateTimeInterface $createdAt = null;
}