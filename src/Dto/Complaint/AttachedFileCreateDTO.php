<?php

namespace App\Dto\Complaint;

class AttachedFileCreateDTO
{
    public ?string $complaintId = null;
    public ?string $fileName = null;
    public ?string $filePath = null;
    public ?string $fileType = null;
    public ?int $fileSize = null;
    public ?string $mimeType = null;
    public ?string $workflowStepId = null;
    public ?string $uploadedById = null;
}
