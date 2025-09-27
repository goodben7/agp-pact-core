<?php

namespace App\Model;

class NewOwnerModel
{
    public function __construct(
        public ?string $code = null,
        public ?string $fullname = null,
        public ?string $sexe = null,
        public ?int $age = null,
        public ?string $phone = null,
        public ?string $identificationNumber = null,
        public ?bool $formerPap = null,
        public ?string $kilometerPoint = null,
        public ?string $typeLiability = null,
        public ?string $province = null,
        public ?string $territory = null,
        public ?string $village = null,
        public ?string $longitude = null,
        public ?string $latitude = null,
        public ?string $referenceCoordinates = null,
        public ?string $orientation = null,
        public ?bool $vulnerability = null,
        public ?string $vulnerabilityType = null,
        public ?string $length = null,
        public ?string $wide = null,
        public ?string $areaAllocatedSquareMeters = null,
        public ?string $cuPerSquareMeter = null,
        public ?string $capitalGain = null,
        public ?string $totalPropertyUsd = null,
        public ?string $totalBatisUsd = null,

        public ?string $commercialActivity = null,
        public ?int $numberWorkingDaysPerWeek = null,
        public ?string $averageDailyIncome = null,
        public ?string $monthlyIncome = null,
        public ?string $totalCompensationThreeMonths = null,
        public ?string $affectedCultivatedArea = null,
        public ?string $equivalentUsd = null,
        public ?string $tree = null,
        public ?string $totalFarmIncome = null,
        public ?string $lossRentalIncome = null,

        public ?string $movingAssistance = null,
        public ?string $assistanceVulnerablePersons = null,
        public ?bool $noticeAgreementVacatingPremises = null,

        public ?string $totalGeneral = null,

        public ?bool $isPaid = null,

        public ?string $remainingAmount = null,

        public ?\DateTimeImmutable $bankAccountCreationDate = null,

        public ?string $bankAccount = null,

        public ?\DateTimeImmutable $paymentDate = null,
    )
    {
    }
}