<?php

namespace App\Dto;

use App\Entity\GeneralParameter;
use Symfony\Component\Validator\Constraints as Assert;

class PrejudiceCreateDTO
{
    public ?string $label = null;

    public ?GeneralParameter $category = null;

    public ?GeneralParameter $complaintType = null;

    public ?string $description = null;

    public ?bool $active = null;

    public ?GeneralParameter $incidentCause = null;

    #[Assert\Valid()]
    /** @var array<\App\Entity\PrejudiceConsequence> */
    public array $consequences = [];
}
