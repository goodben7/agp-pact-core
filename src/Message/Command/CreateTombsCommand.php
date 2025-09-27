<?php

namespace App\Message\Command;

class CreateTombsCommand implements CommandInterface
{
    public function __construct(
        public ?string $code = null,

        public ?string $declarantName = null,

        public ?string $declarantSexe = null,

        public ?int $declarantAge = null,

        public ?string $declarantPhone = null,

        public ?string $village = null,

        public ?string $deceasedNameOrDescriptionVault = null,

        public ?string $placeOfBirthDeceased = null,

        public ?\DateTimeImmutable $dateOfBirthDeceased = null,

        public ?string $deceasedResidence = null,

        public ?string $spouseName = null,

        public ?string $measures = null,

        public ?string $totalGeneral = null,

        public ?bool $isPaid = null,

        public ?string $remainingAmount = null,

        public ?\DateTimeImmutable $bankAccountCreationDate = null,

        public ?string $bankAccount = null,

        public ?\DateTimeImmutable $paymentDate = null,

        public ?string $roadAxis = null,
    )
    {
    }
}