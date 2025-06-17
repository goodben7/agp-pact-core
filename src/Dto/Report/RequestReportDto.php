<?php

namespace App\Dto\Report;

class RequestReportDto
{
    public function __construct(
        public ?string $templateId = null,
        public ?array  $filters = null,
        public ?string $outputFileName = null
    )
    {
    }
}
