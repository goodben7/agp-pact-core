<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Doctrine\IdGenerator;
use App\Dto\Kobo\KoboAssetSnapshotDto;
use App\Dto\ParV2\ValidateParV2Dto;
use App\Dto\ParV2\SyncParV2RequestDto;
use App\Dto\ParV2\SyncParV2ResultDto;
use App\Provider\KoboAssetSnapshotsProvider;
use App\Repository\ParV2Repository;
use App\State\ParV2\SyncParV2Processor;
use App\State\ParV2\ValidateParV2Processor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ParV2Repository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_PAR_V2_KOBO_ID', fields: ['koboId'])]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/parv2s',
            normalizationContext: ['groups' => ['par_v2:list']],
            provider: CollectionProvider::class,
            security: 'is_granted("ROLE_PAR_LIST")'
        ),
        new Get(
            uriTemplate: '/parv2s/{id}',
            normalizationContext: ['groups' => ['par_v2:get']],
            provider: ItemProvider::class,
            security: 'is_granted("ROLE_PAR_DETAILS")'
        ),
        new GetCollection(
            uriTemplate: '/kobo/forms',
            output: KoboAssetSnapshotDto::class,
            provider: KoboAssetSnapshotsProvider::class,
            security: 'is_granted("ROLE_PAR_LIST")'
        ),
        new Post(
            uriTemplate: '/parv2s/sync',
            input: SyncParV2RequestDto::class,
            output: SyncParV2ResultDto::class,
            processor: SyncParV2Processor::class,
            security: 'is_granted("ROLE_PAR_CREATE")'
        ),
        new Post(
            uriTemplate: '/parv2s/validations',
            security: 'is_granted("ROLE_PAR_VALIDATION")',
            input: ValidateParV2Dto::class,
            processor: ValidateParV2Processor::class, 
            status: 200
        ),
    ],
    formats: ['json' => ['application/json']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'koboId' => 'exact',
    'province' => 'exact',
    'axeRoutier' => 'exact',
    'lieuActifAffecte' => 'exact',
    'submittedBy' => 'exact',
    'koboStatus' => 'exact',
    'status' => 'exact',
    'validatedBy' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'submissionTime', 'koboId', 'validatedAt'])]
#[ApiFilter(DateFilter::class, properties: ['startAt', 'endAt', 'dateInventaire', 'submissionTime', 'createdAt', 'validatedAt'])]
class ParV2
{
    public const ID_PREFIX = 'PV';

    public const STATUS_PENDING = 'P';
    public const STATUS_VALIDATED = 'V';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?string $id = null;

    #[ORM\Column(type: Types::BIGINT, unique: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?string $koboId = null;

    #[ORM\Column(length: 64, nullable: true)]
    #[Groups(['par_v2:get'])]
    private ?string $formhubUuid = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?\DateTimeImmutable $endAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?string $province = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?string $axeRoutier = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?string $lieuActifAffecte = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?string $nomPersonneInterview = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?string $qualitePersInterviewe = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?\DateTimeImmutable $dateInventaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?string $gpsActif = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?string $nomChefMenageCm = null;

    #[ORM\Column(length: 8, nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?string $sexeChefMenage = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?int $ageChefMenage = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?string $degreVulnerabiliteChefMenage = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?string $etatCivilChefMenage = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?int $nombreCompositionMenage = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['par_v2:get'])]
    private ?array $groupFx3uw05 = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['par_v2:get'])]
    private ?array $groupCd71m48 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?string $sourceRevenue = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sourceEnergieLumiere = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sourceEnergieCuisine = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sourceEauPotable = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?string $typeActifAffect = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['par_v2:get'])]
    private ?array $groupXh1rg07 = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $etesVousInformeRelocalis = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $acceptezRelocalis = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $conditionRelocalis = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $voulezVousLaisserQuelqu = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bonneChoixRelocaliser = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $enCasProbleme = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?string $photo1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?string $photo2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?string $nomEnqueteur = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?float $dureeInterviewMinutes = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $koboVersion = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $metaInstanceId = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $metaDeprecatedId = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $xformIdString = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $koboUuid = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['par_v2:get'])]
    private ?array $attachments = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?string $koboStatus = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['par_v2:get'])]
    private ?array $geolocation = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?\DateTimeImmutable $submissionTime = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $tags = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $notes = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $validationStatus = null;

    #[ORM\Column(length: 120, nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?string $submittedBy = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $metaRootUuid = null;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['par_v2:get'])]
    private array $rawPayload = [];

    #[ORM\Column(length: 1, options: ['default' => self::STATUS_PENDING], nullable: false)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?string $status = self::STATUS_PENDING;

    #[ORM\Column(nullable: true)]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?\DateTimeImmutable $validatedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'validated_by', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private ?User $validatedBy = null;

    #[ORM\OneToMany(mappedBy: 'par_v2', targetEntity: PaymentHistory::class)]
    #[Groups(['par_v2:get'])]
    private Collection $paymentHistories;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['par_v2:get', 'par_v2:list'])]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->paymentHistories = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getKoboId(): ?string
    {
        return $this->koboId;
    }

    public function setKoboId(string $koboId): static
    {
        $this->koboId = $koboId;

        return $this;
    }

    public function getFormhubUuid(): ?string
    {
        return $this->formhubUuid;
    }

    public function setFormhubUuid(?string $formhubUuid): static
    {
        $this->formhubUuid = $formhubUuid;

        return $this;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(?\DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeImmutable $endAt): static
    {
        $this->endAt = $endAt;

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

    public function getAxeRoutier(): ?string
    {
        return $this->axeRoutier;
    }

    public function setAxeRoutier(?string $axeRoutier): static
    {
        $this->axeRoutier = $axeRoutier;

        return $this;
    }

    public function getLieuActifAffecte(): ?string
    {
        return $this->lieuActifAffecte;
    }

    public function setLieuActifAffecte(?string $lieuActifAffecte): static
    {
        $this->lieuActifAffecte = $lieuActifAffecte;

        return $this;
    }

    public function getNomPersonneInterview(): ?string
    {
        return $this->nomPersonneInterview;
    }

    public function setNomPersonneInterview(?string $nomPersonneInterview): static
    {
        $this->nomPersonneInterview = $nomPersonneInterview;

        return $this;
    }

    public function getQualitePersInterviewe(): ?string
    {
        return $this->qualitePersInterviewe;
    }

    public function setQualitePersInterviewe(?string $qualitePersInterviewe): static
    {
        $this->qualitePersInterviewe = $qualitePersInterviewe;

        return $this;
    }

    public function getDateInventaire(): ?\DateTimeImmutable
    {
        return $this->dateInventaire;
    }

    public function setDateInventaire(?\DateTimeImmutable $dateInventaire): static
    {
        $this->dateInventaire = $dateInventaire;

        return $this;
    }

    public function getGpsActif(): ?string
    {
        return $this->gpsActif;
    }

    public function setGpsActif(?string $gpsActif): static
    {
        $this->gpsActif = $gpsActif;

        return $this;
    }

    public function getNomChefMenageCm(): ?string
    {
        return $this->nomChefMenageCm;
    }

    public function setNomChefMenageCm(?string $nomChefMenageCm): static
    {
        $this->nomChefMenageCm = $nomChefMenageCm;

        return $this;
    }

    public function getSexeChefMenage(): ?string
    {
        return $this->sexeChefMenage;
    }

    public function setSexeChefMenage(?string $sexeChefMenage): static
    {
        $this->sexeChefMenage = $sexeChefMenage;

        return $this;
    }

    public function getAgeChefMenage(): ?int
    {
        return $this->ageChefMenage;
    }

    public function setAgeChefMenage(?int $ageChefMenage): static
    {
        $this->ageChefMenage = $ageChefMenage;

        return $this;
    }

    public function getDegreVulnerabiliteChefMenage(): ?string
    {
        return $this->degreVulnerabiliteChefMenage;
    }

    public function setDegreVulnerabiliteChefMenage(?string $degreVulnerabiliteChefMenage): static
    {
        $this->degreVulnerabiliteChefMenage = $degreVulnerabiliteChefMenage;

        return $this;
    }

    public function getEtatCivilChefMenage(): ?string
    {
        return $this->etatCivilChefMenage;
    }

    public function setEtatCivilChefMenage(?string $etatCivilChefMenage): static
    {
        $this->etatCivilChefMenage = $etatCivilChefMenage;

        return $this;
    }

    public function getNombreCompositionMenage(): ?int
    {
        return $this->nombreCompositionMenage;
    }

    public function setNombreCompositionMenage(?int $nombreCompositionMenage): static
    {
        $this->nombreCompositionMenage = $nombreCompositionMenage;

        return $this;
    }

    public function getGroupFx3uw05(): ?array
    {
        return $this->groupFx3uw05;
    }

    public function setGroupFx3uw05(?array $groupFx3uw05): static
    {
        $this->groupFx3uw05 = $groupFx3uw05;

        return $this;
    }

    public function getGroupCd71m48(): ?array
    {
        return $this->groupCd71m48;
    }

    public function setGroupCd71m48(?array $groupCd71m48): static
    {
        $this->groupCd71m48 = $groupCd71m48;

        return $this;
    }

    public function getSourceRevenue(): ?string
    {
        return $this->sourceRevenue;
    }

    public function setSourceRevenue(?string $sourceRevenue): static
    {
        $this->sourceRevenue = $sourceRevenue;

        return $this;
    }

    public function getSourceEnergieLumiere(): ?string
    {
        return $this->sourceEnergieLumiere;
    }

    public function setSourceEnergieLumiere(?string $sourceEnergieLumiere): static
    {
        $this->sourceEnergieLumiere = $sourceEnergieLumiere;

        return $this;
    }

    public function getSourceEnergieCuisine(): ?string
    {
        return $this->sourceEnergieCuisine;
    }

    public function setSourceEnergieCuisine(?string $sourceEnergieCuisine): static
    {
        $this->sourceEnergieCuisine = $sourceEnergieCuisine;

        return $this;
    }

    public function getSourceEauPotable(): ?string
    {
        return $this->sourceEauPotable;
    }

    public function setSourceEauPotable(?string $sourceEauPotable): static
    {
        $this->sourceEauPotable = $sourceEauPotable;

        return $this;
    }

    public function getTypeActifAffect(): ?string
    {
        return $this->typeActifAffect;
    }

    public function setTypeActifAffect(?string $typeActifAffect): static
    {
        $this->typeActifAffect = $typeActifAffect;

        return $this;
    }

    public function getGroupXh1rg07(): ?array
    {
        return $this->groupXh1rg07;
    }

    public function setGroupXh1rg07(?array $groupXh1rg07): static
    {
        $this->groupXh1rg07 = $groupXh1rg07;

        return $this;
    }

    public function getEtesVousInformeRelocalis(): ?string
    {
        return $this->etesVousInformeRelocalis;
    }

    public function setEtesVousInformeRelocalis(?string $etesVousInformeRelocalis): static
    {
        $this->etesVousInformeRelocalis = $etesVousInformeRelocalis;

        return $this;
    }

    public function getAcceptezRelocalis(): ?string
    {
        return $this->acceptezRelocalis;
    }

    public function setAcceptezRelocalis(?string $acceptezRelocalis): static
    {
        $this->acceptezRelocalis = $acceptezRelocalis;

        return $this;
    }

    public function getConditionRelocalis(): ?string
    {
        return $this->conditionRelocalis;
    }

    public function setConditionRelocalis(?string $conditionRelocalis): static
    {
        $this->conditionRelocalis = $conditionRelocalis;

        return $this;
    }

    public function getVoulezVousLaisserQuelqu(): ?string
    {
        return $this->voulezVousLaisserQuelqu;
    }

    public function setVoulezVousLaisserQuelqu(?string $voulezVousLaisserQuelqu): static
    {
        $this->voulezVousLaisserQuelqu = $voulezVousLaisserQuelqu;

        return $this;
    }

    public function getBonneChoixRelocaliser(): ?string
    {
        return $this->bonneChoixRelocaliser;
    }

    public function setBonneChoixRelocaliser(?string $bonneChoixRelocaliser): static
    {
        $this->bonneChoixRelocaliser = $bonneChoixRelocaliser;

        return $this;
    }

    public function getEnCasProbleme(): ?string
    {
        return $this->enCasProbleme;
    }

    public function setEnCasProbleme(?string $enCasProbleme): static
    {
        $this->enCasProbleme = $enCasProbleme;

        return $this;
    }

    public function getPhoto1(): ?string
    {
        return $this->photo1;
    }

    public function setPhoto1(?string $photo1): static
    {
        $this->photo1 = $photo1;

        return $this;
    }

    public function getPhoto2(): ?string
    {
        return $this->photo2;
    }

    public function setPhoto2(?string $photo2): static
    {
        $this->photo2 = $photo2;

        return $this;
    }

    public function getNomEnqueteur(): ?string
    {
        return $this->nomEnqueteur;
    }

    public function setNomEnqueteur(?string $nomEnqueteur): static
    {
        $this->nomEnqueteur = $nomEnqueteur;

        return $this;
    }

    public function getDureeInterviewMinutes(): ?float
    {
        return $this->dureeInterviewMinutes;
    }

    public function setDureeInterviewMinutes(?float $dureeInterviewMinutes): static
    {
        $this->dureeInterviewMinutes = $dureeInterviewMinutes;

        return $this;
    }

    public function getKoboVersion(): ?string
    {
        return $this->koboVersion;
    }

    public function setKoboVersion(?string $koboVersion): static
    {
        $this->koboVersion = $koboVersion;

        return $this;
    }

    public function getMetaInstanceId(): ?string
    {
        return $this->metaInstanceId;
    }

    public function setMetaInstanceId(?string $metaInstanceId): static
    {
        $this->metaInstanceId = $metaInstanceId;

        return $this;
    }

    public function getMetaDeprecatedId(): ?string
    {
        return $this->metaDeprecatedId;
    }

    public function setMetaDeprecatedId(?string $metaDeprecatedId): static
    {
        $this->metaDeprecatedId = $metaDeprecatedId;

        return $this;
    }

    public function getXformIdString(): ?string
    {
        return $this->xformIdString;
    }

    public function setXformIdString(?string $xformIdString): static
    {
        $this->xformIdString = $xformIdString;

        return $this;
    }

    public function getKoboUuid(): ?string
    {
        return $this->koboUuid;
    }

    public function setKoboUuid(?string $koboUuid): static
    {
        $this->koboUuid = $koboUuid;

        return $this;
    }

    public function getAttachments(): ?array
    {
        return $this->attachments;
    }

    public function setAttachments(?array $attachments): static
    {
        $this->attachments = $attachments;

        return $this;
    }

    public function getKoboStatus(): ?string
    {
        return $this->koboStatus;
    }

    public function setKoboStatus(?string $koboStatus): static
    {
        $this->koboStatus = $koboStatus;

        return $this;
    }

    public function getGeolocation(): ?array
    {
        return $this->geolocation;
    }

    public function setGeolocation(?array $geolocation): static
    {
        $this->geolocation = $geolocation;

        return $this;
    }

    public function getSubmissionTime(): ?\DateTimeImmutable
    {
        return $this->submissionTime;
    }

    public function setSubmissionTime(?\DateTimeImmutable $submissionTime): static
    {
        $this->submissionTime = $submissionTime;

        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    public function getNotes(): ?array
    {
        return $this->notes;
    }

    public function setNotes(?array $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getValidationStatus(): ?array
    {
        return $this->validationStatus;
    }

    public function setValidationStatus(?array $validationStatus): static
    {
        $this->validationStatus = $validationStatus;

        return $this;
    }

    public function getSubmittedBy(): ?string
    {
        return $this->submittedBy;
    }

    public function setSubmittedBy(?string $submittedBy): static
    {
        $this->submittedBy = $submittedBy;

        return $this;
    }

    public function getMetaRootUuid(): ?string
    {
        return $this->metaRootUuid;
    }

    public function setMetaRootUuid(?string $metaRootUuid): static
    {
        $this->metaRootUuid = $metaRootUuid;

        return $this;
    }

    public function getRawPayload(): array
    {
        return $this->rawPayload;
    }

    public function setRawPayload(array $rawPayload): static
    {
        $this->rawPayload = $rawPayload;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getPaymentHistories(): Collection
    {
        return $this->paymentHistories;
    }

    public function addPaymentHistory(PaymentHistory $paymentHistory): static
    {
        if (!$this->paymentHistories->contains($paymentHistory)) {
            $this->paymentHistories->add($paymentHistory);
            $paymentHistory->setPar_v2($this);
        }

        return $this;
    }

    public function removePaymentHistory(PaymentHistory $paymentHistory): static
    {
        if ($this->paymentHistories->removeElement($paymentHistory)) {
            if ($paymentHistory->getPar_v2() === $this) {
                $paymentHistory->setPar_v2(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of status
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */ 
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of validatedAt
     */ 
    public function getValidatedAt(): ?\DateTimeImmutable
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

    /**
     * Get the value of validatedBy
     */ 
    public function getValidatedBy(): ?User
    {
        return $this->validatedBy;
    }

    /**
     * Set the value of validatedBy
     *
     * @return  self
     */ 
    public function setValidatedBy(?User $validatedBy): static
    {
        $this->validatedBy = $validatedBy;

        return $this;
    }
}
