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

    #[Groups(['dashboard_stats:read'])]
    public int $unprocessedComplaints = 0; // (b)

    #[Groups(['dashboard_stats:read'])]
    public int $validatedInProgressComplaints = 0; // (c)

    #[Groups(['dashboard_stats:read'])]
    public int $nonValidatedComplaints = 0; // (d)

    #[Groups(['dashboard_stats:read'])]
    public int $validatedResolvedComplaints = 0; // (e)

    #[Groups(['dashboard_stats:read'])]
    public int $receivabilityUndefinedComplaints = 0;
}
