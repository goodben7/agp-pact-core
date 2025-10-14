<?php

namespace App\Model;

interface TaskInterface
{
    public function getId(): ?string;

    public function getType(): ?string;

    public function getMethod(): ?string;

    public function getStatus(): ?string;

    public function getData(): array;

    public function getCreatedAt(): ?\DateTimeImmutable;

    public function getCreatedBy(): ?string;

    public function getUpdatedAt(): ?\DateTimeImmutable;

    public function getUpdatedBy(): ?string;

    public function getSynchronizedBy(): ?string;

    public function getSynchronizedAt(): ?\DateTimeImmutable;

    public function getDataValue(string $key, mixed $defaultValue = null): mixed;

    public function getMessage(): ?string;

    public function getData1(): ?string;

    public function getData2(): ?string;

    public function getData3(): ?string;

    public function getData4(): ?string;

    public function getData5(): ?string;

    public function getData6(): ?string;

    public function getExternalReferenceId(): ?string;
}