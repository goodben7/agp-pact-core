<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Dto\DashboardStatisticsCategory;
use App\Provider\DashboardStatisticsProvider;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/dashboard_statistics',
            normalizationContext: ['groups' => ['dashboard_stats:read']],
            output: self::class,
            read: true,
            name: 'get_dashboard_statistics',
            provider: DashboardStatisticsProvider::class,
        ),
    ],
    formats: ['json' => ['application/json']],
)]
#[ApiFilter(SearchFilter::class, properties: ['location' => 'exact', 'involvedCompany' => 'exact', 'roadAxisId' => 'exact', 'complaintTypeId' => 'exact'])]
#[ApiFilter(DateFilter::class, properties: ['declarationDate'])]
final class DashboardStatistics
{
    #[Groups(["dashboard_stats:read"])]
    public DashboardStatisticsCategory $general;

    #[Groups(["dashboard_stats:read"])]
    public DashboardStatisticsCategory $sensitive;

    #[Groups(["dashboard_stats:read"])]
    public ?array $papsByVulnerability = null;

    public ?string $location = null;
    public ?string $involvedCompany = null;
    public ?string $roadAxisId = null;
    public ?string $complaintTypeId = null;
    public ?\DateTimeInterface $declarationDate = null;

    public function __construct()
    {
        $this->general = new DashboardStatisticsCategory();
        $this->sensitive = new DashboardStatisticsCategory();
    }
}
