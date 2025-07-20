<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
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
    filters: [
        SearchFilter::class,
        DateFilter::class,
    ],
)]
final class DashboardStatistics
{
    #[Groups(["dashboard_stats:read"])]
    public ?array $complaintsByStatus = null;

    #[Groups(["dashboard_stats:read"])]
    public ?array $complaintsByType = null;

    #[Groups(["dashboard_stats:read"])]
    public ?array $complaintsBySensitivity = null;

    /**
     * @var array{
     *     general: array{total: int, open: int, closed: int, rejected: int},
     *     sensitive: array{total: int, open: int, closed: int, rejected: int}
     * }
     */
    #[Groups(["dashboard_stats:read"])]
    public array $complaintStats = [];

    #[Groups(["dashboard_stats:read"])]
    public ?int $totalComplaints = null;
    #[Groups(["dashboard_stats:read"])]
    public ?int $openComplaints = null;

    #[Groups(["dashboard_stats:read"])]
    public int $totalRejectedComplaints = 0;

    #[Groups(["dashboard_stats:read"])]
    public int $totalSensitiveComplaints = 0;
    #[Groups(["dashboard_stats:read"])]
    public int $openSensitiveComplaints = 0;
    #[Groups(["dashboard_stats:read"])]
    public int $closedSensitiveComplaints = 0;

    #[Groups(["dashboard_stats:read"])]
    public ?float $averageResolutionTimeDays = null;

    #[Groups(["dashboard_stats:read"])]
    public ?array $papsByVulnerability = null;

    #[Groups(["dashboard_stats:read"])]
    public ?array $complaintsDeclaredMonthly = null;

    #[ApiFilter(SearchFilter::class, strategy: "exact", properties: ["location.id" => "exact"])]
    public ?string $locationId = null;

    #[ApiFilter(SearchFilter::class, strategy: "exact", properties: ["complaintType.id" => "exact"])]
    public ?string $complaintTypeId = null;

    #[ApiFilter(DateFilter::class, properties: ["declarationDate"])]
    public ?\DateTimeInterface $startDate = null;

    #[ApiFilter(DateFilter::class, properties: ["declarationDate"])]
    public ?\DateTimeInterface $endDate = null;
}
