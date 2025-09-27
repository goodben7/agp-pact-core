<?php

namespace App\Entity;

use App\Dto\CreateOwnerDto;
use App\Dto\CreateTombsDto;
use App\Dto\ValidateParDto;
use App\Dto\CreateTenantDto;
use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ParRepository;
use ApiPlatform\Metadata\ApiFilter;
use App\State\CreateOwnerProcessor;
use App\State\CreateTombsProcessor;
use App\State\ValidateParProcessor;
use App\State\CreateTenantProcessor;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;

#[ORM\Entity(repositoryClass: ParRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => 'par:get'],
    operations: [
        new Get(
            security: 'is_granted("ROLE_PAR_DETAILS")',
            provider: ItemProvider::class
        ),
        new GetCollection(
            security: 'is_granted("ROLE_PAR_LIST")',
            provider: CollectionProvider::class
        ),
        new Post(
            uriTemplate: "pars/tombs",
            security: 'is_granted("ROLE_PAR_CREATE")',
            input: CreateTombsDto::class,
            processor: CreateTombsProcessor::class,
        ),
        new Post(
            uriTemplate: "pars/owners",
            security: 'is_granted("ROLE_PAR_CREATE")',
            input: CreateOwnerDto::class,
            processor: CreateOwnerProcessor::class,
        ),
        new Post(
            uriTemplate: "pars/tenants",
            security: 'is_granted("ROLE_PAR_CREATE")',
            input: CreateTenantDto::class,
            processor: CreateTenantProcessor::class,
        ),
        new Post(
            uriTemplate: '/pars/validations',
            security: 'is_granted("ROLE_PAR_VALIDATION")',
            input: ValidateParDto::class,
            processor: ValidateParProcessor::class,
            status: 200
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'code' => 'exact',
    'fullname' => 'partial',
    'sexe' => 'exact',
    'age' => 'exact',
    'phone' => 'partial',
    'type' => 'exact',
    'deceasedNameOrDescriptionVault' => 'partial',
    'placeOfBirthDeceased' => 'partial',
    'dateOfBirthDeceased' => 'exact',
    'deceasedResidence' => 'partial',
    'spouseName' => 'partial',
    'measures' => 'partial',
    'identificationNumber' => 'exact',
    'formerPap' => 'exact',
    'kilometerPoint' => 'partial',
    'category' => 'ipartial',
    'typeLiability' => 'exact',
    'province' => 'exact',
    'territory' => 'exact',
    'village' => 'exact',
    'longitude' => 'exact',
    'latitude' => 'exact',
    'orientation' => 'exact',
    'vulnerability' => 'exact',
    'vulnerabilityType' => 'exact',
    'tenantMonthlyRent' => 'exact',
    'lessorName' => 'partial',
    'totalRent' => 'exact',
    'totalLossEmploymentIncome' => 'exact',
    'totalLossBusinessIncome' => 'exact',
    'referenceCoordinates' => 'exact',
    'length' => 'exact',
    'wide' => 'exact',
    'areaAllocatedSquareMeters' => 'exact',
    'cuPerSquareMeter' => 'exact',
    'capitalGain' => 'exact',
    'totalPropertyUsd' => 'exact',
    'totalBatisUsd' => 'exact',
    'commercialActivity' => 'partial',
    'numberWorkingDaysPerWeek' => 'exact',
    'averageDailyIncome' => 'exact',
    'monthlyIncome' => 'exact',
    'totalCompensationThreeMonths' => 'exact',
    'affectedCultivatedArea' => 'exact',
    'equivalentUsd' => 'exact',
    'tree' => 'partial',
    'totalFarmIncome' => 'exact',
    'lossRentalIncome' => 'exact',
    'movingAssistance' => 'exact',
    'assistanceVulnerablePersons' => 'exact',
    'rentalGuaranteeAssistance' => 'exact',
    'noticeAgreementVacatingPremises' => 'exact',
    'totalGeneral' => 'exact',
    'createdAt' => 'exact',
    'isPaid' => 'exact',
    'remainingAmount' => 'exact',
    'bankAccountCreationDate' => 'exact',
    'bankAccount' => 'partial',
    'paymentDate' => 'exact',
    'status' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt'])]
class Par
{
    public const ID_PREFIX = "PA";

    public const TYPE_TOMBS = "TOMBS";
    public const TYPE_TENANT = "TENANT";
    public const TYPE_MINOR = "MINOR";
    public const TYPE_OWNER= "OWNER";
    public const TYPE_OTHER = "OTHER";

    public const ORIENTATION_COTE_DROIT = "CD";
    public const ORIENTATION_COTE_GAUCHE = "CG";

    public const STATUS_PENDING = 'P';
    public const STATUS_VALIDATED = 'V';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['par:get'])]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['par:get'])]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $code = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $fullname = null;

    #[ORM\Column(length: 16, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $sexe = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['par:get'])]
    private ?int $age = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $phone = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $deceasedNameOrDescriptionVault = null;

    #[ORM\Column(length: 16, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $placeOfBirthDeceased = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['par:get'])]
    private ?\DateTimeImmutable $dateOfBirthDeceased = null;

    #[ORM\Column(length: 16, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $deceasedResidence = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $spouseName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $measures = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $identificationNumber = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['par:get'])]
    private ?bool $formerPap = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $kilometerPoint = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $category = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $typeLiability = null;

    #[ORM\Column(length: 16, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $province = null;

    #[ORM\Column(length: 16, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $territory = null;

    #[ORM\Column(length: 16, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $village = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $longitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $latitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $orientation = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['par:get'])]
    private ?bool $vulnerability = null;

    #[ORM\Column(length: 16, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $vulnerabilityType = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $tenantMonthlyRent = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $lessorName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $totalRent = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $totalLossEmploymentIncome = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $totalLossBusinessIncome = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $referenceCoordinates = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $length = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $wide = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $areaAllocatedSquareMeters = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $cuPerSquareMeter = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $capitalGain = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $totalPropertyUsd = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $totalBatisUsd = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $commercialActivity = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['par:get'])]
    private ?int $numberWorkingDaysPerWeek = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $averageDailyIncome = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $monthlyIncome = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $totalCompensationThreeMonths = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $affectedCultivatedArea = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $equivalentUsd = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $tree = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $totalFarmIncome = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $lossRentalIncome = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $movingAssistance = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $assistanceVulnerablePersons = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $rentalGuaranteeAssistance = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['par:get'])]
    private ?bool $noticeAgreementVacatingPremises = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $totalGeneral = null;

    #[ORM\Column]
    #[Groups(['par:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['par:get'])]
    private ?bool $isPaid = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $remainingAmount = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['par:get'])]
    private ?\DateTimeImmutable $bankAccountCreationDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $bankAccount = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['par:get'])]
    private ?\DateTimeImmutable $paymentDate = null;

    #[ORM\Column(length: 16, nullable: true)]
    #[Groups(['par:get'])]
    private ?string $roadAxis = null;

    #[ORM\Column(length: 1, options: ['default' => self::STATUS_PENDING], nullable: false)]
    #[Groups(['par:get'])]
    private ?string $status = self::STATUS_PENDING;

    #[ORM\Column(nullable: true)]
    #[Groups(['par:get'])]
    private ?\DateTimeImmutable $validatedAt = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(?string $fullname): static
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getDeceasedNameOrDescriptionVault(): ?string
    {
        return $this->deceasedNameOrDescriptionVault;
    }

    public function setDeceasedNameOrDescriptionVault(?string $deceasedNameOrDescriptionVault): static
    {
        $this->deceasedNameOrDescriptionVault = $deceasedNameOrDescriptionVault;

        return $this;
    }

    public function getPlaceOfBirthDeceased(): ?string
    {
        return $this->placeOfBirthDeceased;
    }

    public function setPlaceOfBirthDeceased(?string $placeOfBirthDeceased): static
    {
        $this->placeOfBirthDeceased = $placeOfBirthDeceased;

        return $this;
    }

    public function getDateOfBirthDeceased(): ?\DateTimeImmutable
    {
        return $this->dateOfBirthDeceased;
    }

    public function setDateOfBirthDeceased(?\DateTimeImmutable $dateOfBirthDeceased): static
    {
        $this->dateOfBirthDeceased = $dateOfBirthDeceased;

        return $this;
    }

    public function getDeceasedResidence(): ?string
    {
        return $this->deceasedResidence;
    }

    public function setDeceasedResidence(?string $deceasedResidence): static
    {
        $this->deceasedResidence = $deceasedResidence;

        return $this;
    }

    public function getSpouseName(): ?string
    {
        return $this->spouseName;
    }

    public function setSpouseName(?string $spouseName): static
    {
        $this->spouseName = $spouseName;

        return $this;
    }

    public function getMeasures(): ?string
    {
        return $this->measures;
    }

    public function setMeasures(?string $measures): static
    {
        $this->measures = $measures;

        return $this;
    }

    public function getIdentificationNumber(): ?string
    {
        return $this->identificationNumber;
    }

    public function setIdentificationNumber(?string $identificationNumber): static
    {
        $this->identificationNumber = $identificationNumber;

        return $this;
    }

    public function isFormerPap(): ?bool
    {
        return $this->formerPap;
    }

    public function setFormerPap(?bool $formerPap): static
    {
        $this->formerPap = $formerPap;

        return $this;
    }

    public function getKilometerPoint(): ?string
    {
        return $this->kilometerPoint;
    }

    public function setKilometerPoint(?string $kilometerPoint): static
    {
        $this->kilometerPoint = $kilometerPoint;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getTypeLiability(): ?string
    {
        return $this->typeLiability;
    }

    public function setTypeLiability(?string $typeLiability): static
    {
        $this->typeLiability = $typeLiability;

        return $this;
    }

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(?string $province): static
    {
        $this->province = $province;

        return $this;
    }

    public function getTerritory(): ?string
    {
        return $this->territory;
    }

    public function setTerritory(?string $territory): static
    {
        $this->territory = $territory;

        return $this;
    }

    public function getVillage(): ?string
    {
        return $this->village;
    }

    public function setVillage(?string $village): static
    {
        $this->village = $village;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getOrientation(): ?string
    {
        return $this->orientation;
    }

    public function setOrientation(?string $orientation): static
    {
        $this->orientation = $orientation;

        return $this;
    }

    public function isVulnerability(): ?bool
    {
        return $this->vulnerability;
    }

    public function setVulnerability(?bool $vulnerability): static
    {
        $this->vulnerability = $vulnerability;

        return $this;
    }

    public function getVulnerabilityType(): ?string
    {
        return $this->vulnerabilityType;
    }

    public function setVulnerabilityType(?string $vulnerabilityType): static
    {
        $this->vulnerabilityType = $vulnerabilityType;

        return $this;
    }

    public function getTenantMonthlyRent(): ?string
    {
        return $this->tenantMonthlyRent;
    }

    public function setTenantMonthlyRent(?string $tenantMonthlyRent): static
    {
        $this->tenantMonthlyRent = $tenantMonthlyRent;

        return $this;
    }

    public function getLessorName(): ?string
    {
        return $this->lessorName;
    }

    public function setLessorName(?string $lessorName): static
    {
        $this->lessorName = $lessorName;

        return $this;
    }

    public function getTotalRent(): ?string
    {
        return $this->totalRent;
    }

    public function setTotalRent(?string $totalRent): static
    {
        $this->totalRent = $totalRent;

        return $this;
    }

    public function getTotalLossEmploymentIncome(): ?string
    {
        return $this->totalLossEmploymentIncome;
    }

    public function setTotalLossEmploymentIncome(?string $totalLossEmploymentIncome): static
    {
        $this->totalLossEmploymentIncome = $totalLossEmploymentIncome;

        return $this;
    }

    public function getTotalLossBusinessIncome(): ?string
    {
        return $this->totalLossBusinessIncome;
    }

    public function setTotalLossBusinessIncome(?string $totalLossBusinessIncome): static
    {
        $this->totalLossBusinessIncome = $totalLossBusinessIncome;

        return $this;
    }

    public function getReferenceCoordinates(): ?string
    {
        return $this->referenceCoordinates;
    }

    public function setReferenceCoordinates(?string $referenceCoordinates): static
    {
        $this->referenceCoordinates = $referenceCoordinates;

        return $this;
    }

    public function getLength(): ?string
    {
        return $this->length;
    }

    public function setLength(?string $length): static
    {
        $this->length = $length;

        return $this;
    }

    public function getWide(): ?string
    {
        return $this->wide;
    }

    public function setWide(?string $wide): static
    {
        $this->wide = $wide;

        return $this;
    }

    public function getAreaAllocatedSquareMeters(): ?string
    {
        return $this->areaAllocatedSquareMeters;
    }

    public function setAreaAllocatedSquareMeters(?string $areaAllocatedSquareMeters): static
    {
        $this->areaAllocatedSquareMeters = $areaAllocatedSquareMeters;

        return $this;
    }

    public function getCuPerSquareMeter(): ?string
    {
        return $this->cuPerSquareMeter;
    }

    public function setCuPerSquareMeter(?string $cuPerSquareMeter): static
    {
        $this->cuPerSquareMeter = $cuPerSquareMeter;

        return $this;
    }

    public function getCapitalGain(): ?string
    {
        return $this->capitalGain;
    }

    public function setCapitalGain(?string $capitalGain): static
    {
        $this->capitalGain = $capitalGain;

        return $this;
    }

    public function getTotalPropertyUsd(): ?string
    {
        return $this->totalPropertyUsd;
    }

    public function setTotalPropertyUsd(?string $totalPropertyUsd): static
    {
        $this->totalPropertyUsd = $totalPropertyUsd;

        return $this;
    }

    public function getTotalBatisUsd(): ?string
    {
        return $this->totalBatisUsd;
    }

    public function setTotalBatisUsd(?string $totalBatisUsd): static
    {
        $this->totalBatisUsd = $totalBatisUsd;

        return $this;
    }

    public function getCommercialActivity(): ?string
    {
        return $this->commercialActivity;
    }

    public function setCommercialActivity(?string $commercialActivity): static
    {
        $this->commercialActivity = $commercialActivity;

        return $this;
    }

    public function getNumberWorkingDaysPerWeek(): ?int
    {
        return $this->numberWorkingDaysPerWeek;
    }

    public function setNumberWorkingDaysPerWeek(?int $numberWorkingDaysPerWeek): static
    {
        $this->numberWorkingDaysPerWeek = $numberWorkingDaysPerWeek;

        return $this;
    }

    public function getAverageDailyIncome(): ?string
    {
        return $this->averageDailyIncome;
    }

    public function setAverageDailyIncome(?string $averageDailyIncome): static
    {
        $this->averageDailyIncome = $averageDailyIncome;

        return $this;
    }

    public function getMonthlyIncome(): ?string
    {
        return $this->monthlyIncome;
    }

    public function setMonthlyIncome(?string $monthlyIncome): static
    {
        $this->monthlyIncome = $monthlyIncome;

        return $this;
    }

    public function getTotalCompensationThreeMonths(): ?string
    {
        return $this->totalCompensationThreeMonths;
    }

    public function setTotalCompensationThreeMonths(?string $totalCompensationThreeMonths): static
    {
        $this->totalCompensationThreeMonths = $totalCompensationThreeMonths;

        return $this;
    }

    public function getAffectedCultivatedArea(): ?string
    {
        return $this->affectedCultivatedArea;
    }

    public function setAffectedCultivatedArea(?string $affectedCultivatedArea): static
    {
        $this->affectedCultivatedArea = $affectedCultivatedArea;

        return $this;
    }

    public function getEquivalentUsd(): ?string
    {
        return $this->equivalentUsd;
    }

    public function setEquivalentUsd(?string $equivalentUsd): static
    {
        $this->equivalentUsd = $equivalentUsd;

        return $this;
    }

    public function getTree(): ?string
    {
        return $this->tree;
    }

    public function setTree(?string $tree): static
    {
        $this->tree = $tree;

        return $this;
    }

    public function getTotalFarmIncome(): ?string
    {
        return $this->totalFarmIncome;
    }

    public function setTotalFarmIncome(?string $totalFarmIncome): static
    {
        $this->totalFarmIncome = $totalFarmIncome;

        return $this;
    }

    public function getLossRentalIncome(): ?string
    {
        return $this->lossRentalIncome;
    }

    public function setLossRentalIncome(?string $lossRentalIncome): static
    {
        $this->lossRentalIncome = $lossRentalIncome;

        return $this;
    }

    public function getMovingAssistance(): ?string
    {
        return $this->movingAssistance;
    }

    public function setMovingAssistance(?string $movingAssistance): static
    {
        $this->movingAssistance = $movingAssistance;

        return $this;
    }

    public function getAssistanceVulnerablePersons(): ?string
    {
        return $this->assistanceVulnerablePersons;
    }

    public function setAssistanceVulnerablePersons(?string $assistanceVulnerablePersons): static
    {
        $this->assistanceVulnerablePersons = $assistanceVulnerablePersons;

        return $this;
    }

    public function getRentalGuaranteeAssistance(): ?string
    {
        return $this->rentalGuaranteeAssistance;
    }

    public function setRentalGuaranteeAssistance(?string $rentalGuaranteeAssistance): static
    {
        $this->rentalGuaranteeAssistance = $rentalGuaranteeAssistance;

        return $this;
    }

    public function isNoticeAgreementVacatingPremises(): ?bool
    {
        return $this->noticeAgreementVacatingPremises;
    }

    public function setNoticeAgreementVacatingPremises(?bool $noticeAgreementVacatingPremises): static
    {
        $this->noticeAgreementVacatingPremises = $noticeAgreementVacatingPremises;

        return $this;
    }

    public function getTotalGeneral(): ?string
    {
        return $this->totalGeneral;
    }

    public function setTotalGeneral(?string $totalGeneral): static
    {
        $this->totalGeneral = $totalGeneral;

        return $this;
    }

    /**
     * Get the value of createdAt
     */ 
    public function getCreatedAt(): \DateTimeImmutable|null
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */ 
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(?bool $isPaid): static
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function getRemainingAmount(): ?string
    {
        return $this->remainingAmount;
    }

    public function setRemainingAmount(?string $remainingAmount): static
    {
        $this->remainingAmount = $remainingAmount;

        return $this;
    }

    public function getBankAccountCreationDate(): ?\DateTimeImmutable
    {
        return $this->bankAccountCreationDate;
    }

    public function setBankAccountCreationDate(?\DateTimeImmutable $bankAccountCreationDate): static
    {
        $this->bankAccountCreationDate = $bankAccountCreationDate;

        return $this;
    }

    public function getBankAccount(): ?string
    {
        return $this->bankAccount;
    }

    public function setBankAccount(?string $bankAccount): static
    {
        $this->bankAccount = $bankAccount;

        return $this;
    }

    public function getPaymentDate(): ?\DateTimeImmutable
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(?\DateTimeImmutable $paymentDate): static
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    /**
     * Get the value of roadAxis
     */ 
    public function getRoadAxis(): string|null
    {
        return $this->roadAxis;
    }

    /**
     * Set the value of roadAxis
     *
     * @return  self
     */ 
    public function setRoadAxis(?string $roadAxis): static
    {
        $this->roadAxis = $roadAxis;

        return $this;
    }

    /**
     * Get the value of status
     */ 
    public function getStatus(): string|null
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */ 
    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of validatedAt
     */ 
    public function getValidatedAt(): \DateTimeImmutable|null
    {
        return $this->validatedAt;
    }

    /**
     * Set the value of validatedAt
     *
     * @return  self
     */ 
    public function setValidatedAt(?\DateTimeImmutable $validatedAt): static
    {
        $this->validatedAt = $validatedAt;

        return $this;
    }
}