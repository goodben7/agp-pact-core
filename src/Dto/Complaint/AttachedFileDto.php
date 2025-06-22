<?php

namespace App\Dto\Complaint;

use App\Entity\GeneralParameter;
use App\Entity\WorkflowStep;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class AttachedFileDto
{
    #[Assert\NotBlank]
    public ?string $complaintId = null;

    public ?UploadedFile $file = null;

    public ?GeneralParameter $fileType = null;

    public ?string $fileName = null;

    public ?WorkflowStep $workflowStep = null;
}
