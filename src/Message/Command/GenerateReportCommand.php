<?php

namespace App\Message\Command;

class GenerateReportCommand implements CommandInterface
{
    public function __construct(
        public string  $reportTemplateId,
        public array   $filters,
        public string  $requestedByUserId,
        public ?string $outputFileName = null
    )
    {
    }

    public function getReportTemplateId(): string
    {
        return $this->reportTemplateId;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getRequestedByUserId(): string
    {
        return $this->requestedByUserId;
    }

    public function getOutputFileName(): ?string
    {
        return $this->outputFileName;
    }
}
