<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

class ParStatisticsCategory
{
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
}