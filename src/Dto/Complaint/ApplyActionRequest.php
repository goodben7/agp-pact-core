<?php

namespace App\Dto\Complaint;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class ApplyActionRequest
{
    public ?\DateTimeInterface $encodedAt = null;

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
        if (isset($data['encodedAt'])) {
            $value = $data['encodedAt'];
            if (is_string($value) && $value !== '') {
                try {
                    $this->encodedAt = new \DateTimeImmutable($value);
                } catch (\Exception $e) {
                    $this->encodedAt = null;
                }
            } elseif ($value instanceof \DateTimeInterface) {
                $this->encodedAt = \DateTimeImmutable::createFromInterface($value);
            }
            unset($data['encodedAt']);
        }

        $this->dynamicFields = $data;

        return $this;
    }

    public function toArray(): array
    {
        $arr = $this->dynamicFields;
        if ($this->encodedAt instanceof \DateTimeInterface) {
            $arr['encodedAt'] = $this->encodedAt->format(DATE_ATOM);
        }
        return $arr;
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
