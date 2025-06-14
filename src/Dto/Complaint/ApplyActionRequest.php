<?php

namespace App\Dto\Complaint;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class ApplyActionRequest
{
    #[Assert\NotBlank]
    public ?string $actionId = null;

    public ?string $comment = null;

    public ?File $file = null;

    public ?string $internalResolutionDecisionId = null;
    public ?string $justification = null;

    public array $dynamicFields = [];

    public function toArray(): array
    {
        $data = [
            'comment' => $this->comment,
            'justification' => $this->justification,
        ];

        if ($this->internalResolutionDecisionId !== null) {
            $data['internalResolutionDecisionId'] = $this->internalResolutionDecisionId;
        }

        if ($this->file !== null) {
            $data['file'] = $this->file;
        }

        return array_merge($data, $this->dynamicFields);
    }
}
