<?php

namespace App\Dto\Complaint;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class ApplyActionRequest
{
    #[Assert\NotBlank]
    public ?string $actionId = null;

    public array $dynamicFields = [];

    public function __construct()
    {
        $this->dynamicFields = [];
    }

    public function setFromArray(array $data): self
    {
        if (isset($data['actionId'])) {
            $this->actionId = $data['actionId'];
            unset($data['actionId']);
        }

        $this->dynamicFields = $data;

        return $this;
    }

    public function toArray(): array
    {
        return $this->dynamicFields;
    }

    public function hasFile(): bool
    {
        foreach ($this->dynamicFields as $value) {
            if ($value instanceof UploadedFile) {
                return true;
            }
        }
        return false;
    }

    public function getFiles(): array
    {
        $files = [];
        foreach ($this->dynamicFields as $key => $value) {
            if ($value instanceof UploadedFile) {
                $files[$key] = $value;
            }
        }
        return $files;
    }
}
