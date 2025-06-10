<?php

namespace App\Dto\Complaint;

class AttachedFileUpdateDTO
{
    public ?string $fileName = null;
    public ?string $filePath = null;
    public ?string $fileType = null;
    public ?string $workflowStepId = null;
}
