<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

final class DashboardStatisticsCategory
{
    #[Groups(["dashboard_stats:read"])]
    public array $complaintsByStatus = [];

    #[Groups(["dashboard_stats:read"])]
    public array $complaintsByType = [];

    #[Groups(["dashboard_stats:read"])]
    public int $totalComplaints = 0;

    #[Groups(["dashboard_stats:read"])]
    public int $openComplaints = 0;

    #[Groups(["dashboard_stats:read"])]
    public int $closedComplaints = 0;

    #[Groups(["dashboard_stats:read"])]
    public int $rejectedComplaints = 0;

    #[Groups(["dashboard_stats:read"])]
    public ?float $averageResolutionTimeDays = null;

    #[Groups(["dashboard_stats:read"])]
    public array $complaintsDeclaredMonthly = [];
}
