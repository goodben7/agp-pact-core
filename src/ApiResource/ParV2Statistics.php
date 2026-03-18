<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Provider\ParV2StatisticsProvider;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/parv2_statistics',
            normalizationContext: ['groups' => ['par_v2_stats:read']],
            output: self::class,
            read: true,
            name: 'get_parv2_statistics',
            provider: ParV2StatisticsProvider::class, 
        ),
    ],
    formats: ['json' => ['application/json']],
)]
#[ApiFilter(SearchFilter::class, properties: [
    'province' => 'exact',
    'axeRoutier' => 'exact',
    'lieuActifAffecte' => 'exact',
    'submittedBy' => 'exact',
    'koboStatus' => 'exact',
    'sexeChefMenage' => 'exact',
    'typeActifAffect' => 'exact',
])]
#[ApiFilter(DateFilter::class, properties: ['dateInventaire', 'submissionTime', 'createdAt'])]
final class ParV2Statistics
{
    #[Groups(['par_v2_stats:read'])]
    public int $totalRecords = 0;

    #[Groups(['par_v2_stats:read'])]
    public int $withGps = 0;

    #[Groups(['par_v2_stats:read'])]
    public int $withPhoto1 = 0;

    #[Groups(['par_v2_stats:read'])]
    public int $withPhoto2 = 0;

    #[Groups(['par_v2_stats:read'])]
    public int $withAttachments = 0;

    #[Groups(['par_v2_stats:read'])]
    public float $averageAgeChefMenage = 0.0;

    #[Groups(['par_v2_stats:read'])]
    public array $byProvince = [];

    #[Groups(['par_v2_stats:read'])]
    public array $byAxeRoutier = [];

    #[Groups(['par_v2_stats:read'])]
    public array $byLieuActifAffecte = [];

    #[Groups(['par_v2_stats:read'])]
    public array $byKoboStatus = [];

    #[Groups(['par_v2_stats:read'])]
    public array $bySubmittedBy = [];

    #[Groups(['par_v2_stats:read'])]
    public array $byTypeActifAffect = [];

    #[Groups(['par_v2_stats:read'])]
    public array $byGenderChefMenage = [];

    #[Groups(['par_v2_stats:read'])]
    public array $inventoriesMonthly = [];

    public ?string $province = null;
    public ?string $axeRoutier = null;
    public ?string $lieuActifAffecte = null;
    public ?string $submittedBy = null;
    public ?string $koboStatus = null;
    public ?string $sexeChefMenage = null;
    public ?string $typeActifAffect = null;
    public ?\DateTimeInterface $dateInventaire = null;
    public ?\DateTimeInterface $submissionTime = null;
    public ?\DateTimeInterface $createdAt = null;
}

