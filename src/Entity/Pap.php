<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PapRepository;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PapRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("ROLE_PAP_DETAILS")',
            provider: ItemProvider::class
        ),
        new GetCollection(
            security: 'is_granted("ROLE_PAP_LIST")',
            provider: CollectionProvider::class
        ),
        new Post(
            denormalizationContext: ['groups' => 'pap:post',],
            security: 'is_granted("ROLE_PAP_CREATE")',
            processor: PersistProcessor::class,
        ),
        new Patch(
            denormalizationContext: ['groups' => 'pap:patch',],
            security: 'is_granted("ROLE_PAP_UPDATE")',
            processor: PersistProcessor::class,
        ),
    ],
    normalizationContext: ['groups' => 'pap:get']
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'code' => 'exact',
    'fullName' => 'partial',
    'gender' => 'exact',
    'age' => 'exact',
    'personType.code' => 'exact',
    'personType.category' => 'exact',
    'vulnerabilityDegree.code' => 'exact',
    'vulnerabilityDegree.category' => 'exact',
    'orientation' => 'exact',
    'propertyType' => 'exact',
    'territory.code' => 'exact',
    'village.code' => 'exact',
    'territory.category' => 'exact',
    'village.category' => 'exact',
    'province.code' => 'exact',
    'province.category' => 'exact',
    'category' => 'ipartial'
])]
class Pap
{
    public const ID_PREFIX = "PA";

    public const ORIENTATION_CD = "CD";
    public const ORIENTATION_CG = "CG";

    public const PROPERTY_TYPE_PROPRIETAIRE = "P";
    public const PROPERTY_TYPE_REPRESENTANT = "R";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['pap:get'])]
    private ?string $id = null;

    #[ORM\Column(length: 120)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?string $code = null; // CODE PAP

    #[ORM\Column(length: 255)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?string $fullName = null; // Nom Post-nom et Prénom de la PAP

    #[ORM\Column(length: 10, nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?string $gender = null; // Sexe

    #[ORM\Column(nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?int $age = null; // Age

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?GeneralParameter $personType = null; // Type de personne (doit être dérivé ou ajouté au CSV)

    #[ORM\Column(length: 15, nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?string $contactPhoneNumber = null; // Numéro téléphonique

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?string $identificationNumber = null; // Numéro de la pièce d’identité de la PAP

    #[ORM\Column(length: 180, nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?string $contactEmail = null; // Non présent dans le CSV, mais était dans votre classe

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?GeneralParameter $vulnerabilityDegree = null; // Vulnérabilité / Type de vulnérabilité


    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?string $referenceKilometerPoint = null; // Point Kilométrique de réference

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Choice(choices: [self::ORIENTATION_CD, self::ORIENTATION_CG])]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?string $orientation = null; // Orientation

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Choice(choices: [self::PROPERTY_TYPE_PROPRIETAIRE, self::PROPERTY_TYPE_REPRESENTANT])]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?string $propertyType = null; // Type de propriété

    // Land Assets
    #[ORM\Column(nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?float $landAffectedSurface = null; // Surface en m² (Actifs fonciers)

    #[ORM\Column(nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?float $landCu = null; // CU par m² (Catég A=30$)

    #[ORM\Column(nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?float $landAddedValue = null; // Plus value 5% (Actifs fonciers)

    // Built Assets
    #[ORM\Column(nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?float $buildingAffectedSurface = null; // Maisons Surface en m²

    #[ORM\Column(nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?float $annexSurface = null; // ANNEXE - Surface en m²

    #[ORM\Column(nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?float $buildingCu = null; // CU par m² (CatégA=100$, B=50$, C=30$)

    #[ORM\Column(nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?float $buildingAddedValue = null; // Plus value 5% (Actifs batis)

    // Commercial Income
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?string $commercialActivityAffected = null; // Activité commerciale

    #[ORM\Column(nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?int $numberOfDaysAffectedPerWeek = null; // Nombre de jour de travail par semaine

    // Agricultural Assets
    #[ORM\Column(nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?float $cultivatedAffectedSurface = null; // Superficie cultivée affectée (m²)

    #[ORM\Column(nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?int $affectedTrees = null; // Arbres affectés

    #[ORM\Column(nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?float $rentalIncomeLoss = null; // Perte de revenu locatif

    #[ORM\Column(nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?float $relocationAssistance = null; // Assistance au demenagement

    #[ORM\Column(nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?float $vulnerablePersonAssistance = null; // Assistance aux personnes vulnerables

    #[ORM\Column(nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?float $totalDollarEquivalent = null; // TOTAL GENERAL

    #[ORM\Column]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?bool $siteReleaseAgreement = null; // Accord de liberation de lieu

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?Location $province = null; // Correspond à  la "Province" du CSV

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?Location $territory = null; // Correspond à "Territoire" du CSV

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?Location $village = null; // Correspond à "Village dans KABINDA MBANGA" du CSV

    #[ORM\Column(nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?float $longitude = null; // Correspond à "Longitude" du CSV

    #[ORM\Column(nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?float $latitude = null; // Correspond à "Latitude" du CSV

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['pap:get', 'pap:post', 'pap:patch'])]
    private ?string $category = null; // Catégorie PAP

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): static
    {
        $this->gender = $gender;

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

    public function getPersonType(): ?GeneralParameter
    {
        return $this->personType;
    }

    public function setPersonType(?GeneralParameter $personType): static
    {
        $this->personType = $personType;

        return $this;
    }

    public function getContactPhoneNumber(): ?string
    {
        return $this->contactPhoneNumber;
    }

    public function setContactPhoneNumber(?string $contactPhoneNumber): static
    {
        $this->contactPhoneNumber = $contactPhoneNumber;

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

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(?string $contactEmail): static
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    public function getVulnerabilityDegree(): ?GeneralParameter
    {
        return $this->vulnerabilityDegree;
    }

    public function setVulnerabilityDegree(?GeneralParameter $vulnerabilityDegree): static
    {
        $this->vulnerabilityDegree = $vulnerabilityDegree;

        return $this;
    }

    public function getReferenceKilometerPoint(): ?string
    {
        return $this->referenceKilometerPoint;
    }

    public function setReferenceKilometerPoint(?string $referenceKilometerPoint): static
    {
        $this->referenceKilometerPoint = $referenceKilometerPoint;

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

    public function getLandAffectedSurface(): ?float
    {
        return $this->landAffectedSurface;
    }

    public function setLandAffectedSurface(?float $landAffectedSurface): static
    {
        $this->landAffectedSurface = $landAffectedSurface;

        return $this;
    }

    public function getLandCu(): ?float
    {
        return $this->landCu;
    }

    public function setLandCu(?float $landCu): static
    {
        $this->landCu = $landCu;

        return $this;
    }

    public function getLandAddedValue(): ?float
    {
        return $this->landAddedValue;
    }

    public function setLandAddedValue(?float $landAddedValue): static
    {
        $this->landAddedValue = $landAddedValue;

        return $this;
    }

    public function getBuildingAffectedSurface(): ?float
    {
        return $this->buildingAffectedSurface;
    }

    public function setBuildingAffectedSurface(?float $buildingAffectedSurface): static
    {
        $this->buildingAffectedSurface = $buildingAffectedSurface;

        return $this;
    }

    public function getAnnexSurface(): ?float
    {
        return $this->annexSurface;
    }

    public function setAnnexSurface(?float $annexSurface): static
    {
        $this->annexSurface = $annexSurface;

        return $this;
    }

    public function getBuildingCu(): ?float
    {
        return $this->buildingCu;
    }

    public function setBuildingCu(?float $buildingCu): static
    {
        $this->buildingCu = $buildingCu;

        return $this;
    }

    public function getBuildingAddedValue(): ?float
    {
        return $this->buildingAddedValue;
    }

    public function setBuildingAddedValue(?float $buildingAddedValue): static
    {
        $this->buildingAddedValue = $buildingAddedValue;

        return $this;
    }

    public function getCommercialActivityAffected(): ?string
    {
        return $this->commercialActivityAffected;
    }

    public function setCommercialActivityAffected(?string $commercialActivityAffected): static
    {
        $this->commercialActivityAffected = $commercialActivityAffected;

        return $this;
    }

    public function getNumberOfDaysAffectedPerWeek(): ?int
    {
        return $this->numberOfDaysAffectedPerWeek;
    }

    public function setNumberOfDaysAffectedPerWeek(?int $numberOfDaysAffectedPerWeek): static
    {
        $this->numberOfDaysAffectedPerWeek = $numberOfDaysAffectedPerWeek;

        return $this;
    }

    public function getCultivatedAffectedSurface(): ?float
    {
        return $this->cultivatedAffectedSurface;
    }

    public function setCultivatedAffectedSurface(?float $cultivatedAffectedSurface): static
    {
        $this->cultivatedAffectedSurface = $cultivatedAffectedSurface;

        return $this;
    }

    public function getAffectedTrees(): ?int
    {
        return $this->affectedTrees;
    }

    public function setAffectedTrees(?int $affectedTrees): static
    {
        $this->affectedTrees = $affectedTrees;

        return $this;
    }

    public function getRentalIncomeLoss(): ?float
    {
        return $this->rentalIncomeLoss;
    }

    public function setRentalIncomeLoss(?float $rentalIncomeLoss): static
    {
        $this->rentalIncomeLoss = $rentalIncomeLoss;

        return $this;
    }

    public function getRelocationAssistance(): ?float
    {
        return $this->relocationAssistance;
    }

    public function setRelocationAssistance(?float $relocationAssistance): static
    {
        $this->relocationAssistance = $relocationAssistance;

        return $this;
    }

    public function getVulnerablePersonAssistance(): ?float
    {
        return $this->vulnerablePersonAssistance;
    }

    public function setVulnerablePersonAssistance(?float $vulnerablePersonAssistance): static
    {
        $this->vulnerablePersonAssistance = $vulnerablePersonAssistance;

        return $this;
    }

    public function getTotalDollarEquivalent(): ?float
    {
        return $this->totalDollarEquivalent;
    }

    public function setTotalDollarEquivalent(?float $totalDollarEquivalent): static
    {
        $this->totalDollarEquivalent = $totalDollarEquivalent;

        return $this;
    }

    public function isSiteReleaseAgreement(): ?bool
    {
        return $this->siteReleaseAgreement;
    }

    public function setSiteReleaseAgreement(bool $siteReleaseAgreement): static
    {
        $this->siteReleaseAgreement = $siteReleaseAgreement;

        return $this;
    }

    /**
     * Get the value of code
     */
    public function getCode(): string|null
    {
        return $this->code;
    }

    /**
     * Set the value of code
     *
     * @return  self
     */
    public function setCode($code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get the value of territory
     */
    public function getTerritory(): Location|null
    {
        return $this->territory;
    }

    /**
     * Set the value of territory
     *
     * @return  self
     */
    public function setTerritory(?Location $territory): static
    {
        $this->territory = $territory;

        return $this;
    }

    /**
     * Get the value of village
     */
    public function getVillage(): Location|null
    {
        return $this->village;
    }

    /**
     * Set the value of village
     *
     * @return  self
     */
    public function setVillage(?Location $village): static
    {
        $this->village = $village;

        return $this;
    }

    /**
     * Get the value of longitude
     */
    public function getLongitude(): float|null
    {
        return $this->longitude;
    }

    /**
     * Set the value of longitude
     *
     * @return  self
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get the value of latitude
     */
    public function getLatitude(): float|null
    {
        return $this->latitude;
    }

    /**
     * Set the value of latitude
     *
     * @return  self
     */
    public function setLatitude($latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get the value of province
     */
    public function getProvince(): Location|null
    {
        return $this->province;
    }

    /**
     * Set the value of province
     *
     * @return  self
     */
    public function setProvince(?Location $province): static
    {
        $this->province = $province;

        return $this;
    }

    /**
     * Get the value of category
     */
    public function getCategory(): string|null
    {
        return $this->category;
    }

    /**
     * Set the value of category
     *
     * @return  self
     */
    public function setCategory($category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get the value of propertyType
     */
    public function getPropertyType(): string|null
    {
        return $this->propertyType;
    }

    /**
     * Set the value of propertyType
     *
     * @return  self
     */
    public function setPropertyType($propertyType)
    {
        $this->propertyType = $propertyType;

        return $this;
    }
}
