<?php

namespace App\Dto\Company;

use Symfony\Component\Serializer\Annotation\Groups;

final class CompanyStatisticsDto
{
    #[Groups(["company_stats:read"])]
    public string $companyId;

    #[Groups(["company_stats:read"])]
    public string $companyName;

    #[Groups(["company_stats:read"])]
    public int $complaintCount;

    public function __construct(string $companyId, string $companyName, int $complaintCount)
    {
        $this->companyId = $companyId;
        $this->companyName = $companyName;
        $this->complaintCount = $complaintCount;
    }
}
