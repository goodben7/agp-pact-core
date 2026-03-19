<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Provider\Complaint\ComplaintListLightProvider;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/complaints_light',
            normalizationContext: ['groups' => ['complaint_light:list']],
            output: self::class,
            read: true,
            name: 'get_complaints_light',
            provider: ComplaintListLightProvider::class,
            //security: "is_granted('ROLE_COMPLAINT_LIST')"
        ),
    ],
    formats: ['json' => ['application/json']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'complaintType' => 'exact',
    'currentWorkflowStep' => 'exact',
    'roadAxis' => 'exact',
    'location' => 'exact',
    'involvedCompany' => 'exact',
    'isSensitive' => 'exact',
    'isReceivable' => 'exact',
    'closed' => 'exact',
])]
#[ApiFilter(DateFilter::class, properties: ['declarationDate', 'incidentDate', 'closureDate'])]
#[ApiFilter(OrderFilter::class, properties: ['declarationDate', 'incidentDate', 'closureDate'])]
final class ComplaintListLight
{
    #[Groups(['complaint_light:list'])]
    public string $id;

    #[Groups(['complaint_light:list'])]
    public ?string $complaintTypeId = null;

    #[Groups(['complaint_light:list'])]
    public ?string $complaintTypeValue = null;

    #[Groups(['complaint_light:list'])]
    public ?string $workflowStepId = null;

    #[Groups(['complaint_light:list'])]
    public ?string $workflowStepName = null;

    #[Groups(['complaint_light:list'])]
    public ?\DateTimeImmutable $declarationDate = null;

    #[Groups(['complaint_light:list'])]
    public ?\DateTimeImmutable $incidentDate = null;

    #[Groups(['complaint_light:list'])]
    public ?\DateTimeImmutable $closureDate = null;

    #[Groups(['complaint_light:list'])]
    public ?bool $closed = null;

    #[Groups(['complaint_light:list'])]
    public ?bool $isReceivable = null;

    #[Groups(['complaint_light:list'])]
    public ?bool $isSensitive = null;

    #[Groups(['complaint_light:list'])]
    public ?string $categoryLabel = null;

    #[Groups(['complaint_light:list'])]
    public ?string $locationId = null;

    #[Groups(['complaint_light:list'])]
    public ?string $locationName = null;

    #[Groups(['complaint_light:list'])]
    public ?string $roadAxisId = null;

    #[Groups(['complaint_light:list'])]
    public ?string $roadAxisName = null;

    #[Groups(['complaint_light:list'])]
    public ?string $involvedCompanyId = null;

    #[Groups(['complaint_light:list'])]
    public ?string $involvedCompanyName = null;

    #[Groups(['complaint_light:list'])]
    public ?string $complainantId = null;

    #[Groups(['complaint_light:list'])]
    public ?string $complainantName = null;

    #[Groups(['complaint_light:list'])]
    public ?string $complainantPhone = null;

    #[Groups(['complaint_light:list'])]
    public ?string $treatmentLevel = null;

    #[Groups(['complaint_light:list'])]
    public ?string $currentAssigneeId = null;

    #[Groups(['complaint_light:list'])]
    public ?string $currentAssigneeName = null;

    #[Groups(['complaint_light:list'])]
    public ?string $currentAssignedCompanyId = null;

    #[Groups(['complaint_light:list'])]
    public ?string $currentAssignedCompanyName = null;
}
