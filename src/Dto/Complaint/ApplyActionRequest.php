<?php

namespace App\Dto\Complaint;

use Symfony\Component\Validator\Constraints as Assert;

class ApplyActionRequest
{
    #[Assert\NotBlank]
    public ?string $actionId = null;
    public array $dynamicFields = [];

    public function toArray(): array
    {
        return $this->dynamicFields;
    }
}
