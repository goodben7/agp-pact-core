<?php

namespace App\Provider\Complaint;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\ComplaintListLight;
use App\Entity\Complaint;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class ComplaintListLightProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $filters = $context['filters'] ?? [];

        $qb = $this->entityManager->createQueryBuilder()
            ->select(
                'c.id as id',
                'c.declarationDate as declarationDate',
                'c.incidentDate as incidentDate',
                'c.closureDate as closureDate',
                'c.closed as closed',
                'c.isReceivable as isReceivable',
                'c.isSensitive as isSensitive',
                "CASE WHEN c.isSensitive IS NULL THEN 'Générale' WHEN c.isSensitive = true THEN 'Hypersensible' ELSE 'Sensible' END as categoryLabel",
                'ct.id as complaintTypeId',
                'ct.value as complaintTypeValue',
                'ws.id as workflowStepId',
                'ws.name as workflowStepName',
                'wsc.title as treatmentLevel',
                'l.id as locationId',
                'l.name as locationName',
                'ra.id as roadAxisId',
                'ra.name as roadAxisName',
                'ic.id as involvedCompanyId',
                'ic.name as involvedCompanyName',
                'cp.id as complainantId',
                'cp.displayName as complainantName',
                'cp.contactPhone as complainantPhone',
                'ca.id as currentAssigneeId',
                'ca.displayName as currentAssigneeName',
                'cac.id as currentAssignedCompanyId',
                'cac.name as currentAssignedCompanyName'
            )
            ->from(Complaint::class, 'c')
            ->leftJoin('c.complaintType', 'ct')
            ->leftJoin('c.currentWorkflowStep', 'ws')
            ->leftJoin('ws.uiConfiguration', 'wsc')
            ->leftJoin('c.location', 'l')
            ->leftJoin('c.roadAxis', 'ra')
            ->leftJoin('c.involvedCompany', 'ic')
            ->leftJoin('c.complainant', 'cp')
            ->leftJoin('c.currentAssignee', 'ca')
            ->leftJoin('c.currentAssignedCompany', 'cac');

        $this->applyFilters($qb, $filters);
        $this->applyPagination($qb);
        $this->applyOrdering($qb, $filters);

        $rows = $qb->getQuery()->getArrayResult();

        $items = [];
        foreach ($rows as $row) {
            $dto = new ComplaintListLight();
            $dto->id = (string) $row['id'];
            $dto->declarationDate = $row['declarationDate'] ?? null;
            $dto->incidentDate = $row['incidentDate'] ?? null;
            $dto->closureDate = $row['closureDate'] ?? null;
            $dto->closed = $row['closed'] ?? null;
            $dto->isReceivable = $row['isReceivable'] ?? null;
            $dto->isSensitive = $row['isSensitive'] ?? null;
            $dto->categoryLabel = $row['categoryLabel'] ?? null;
            $dto->complaintTypeId = $row['complaintTypeId'] ?? null;
            $dto->complaintTypeValue = $row['complaintTypeValue'] ?? null;
            $dto->workflowStepId = $row['workflowStepId'] ?? null;
            $dto->workflowStepName = $row['workflowStepName'] ?? null;
            $dto->treatmentLevel = $row['treatmentLevel'] ?? null;
            $dto->locationId = $row['locationId'] ?? null;
            $dto->locationName = $row['locationName'] ?? null;
            $dto->roadAxisId = $row['roadAxisId'] ?? null;
            $dto->roadAxisName = $row['roadAxisName'] ?? null;
            $dto->involvedCompanyId = $row['involvedCompanyId'] ?? null;
            $dto->involvedCompanyName = $row['involvedCompanyName'] ?? null;
            $dto->complainantId = $row['complainantId'] ?? null;
            $dto->complainantName = $row['complainantName'] ?? null;
            $dto->complainantPhone = $row['complainantPhone'] ?? null;
            $dto->currentAssigneeId = $row['currentAssigneeId'] ?? null;
            $dto->currentAssigneeName = $row['currentAssigneeName'] ?? null;
            $dto->currentAssignedCompanyId = $row['currentAssignedCompanyId'] ?? null;
            $dto->currentAssignedCompanyName = $row['currentAssignedCompanyName'] ?? null;

            $items[] = $dto;
        }

        return $items;
    }

    private function applyFilters(QueryBuilder $qb, array $filters): void
    {
        $qb->andWhere('c.deleted = false');

        if (!empty($filters['id'])) {
            $qb->andWhere('c.id = :id')->setParameter('id', $filters['id']);
        }
        if (!empty($filters['complaintType'])) {
            $qb->andWhere('ct.id = :complaintType')->setParameter('complaintType', $filters['complaintType']);
        }
        if (!empty($filters['currentWorkflowStep'])) {
            $qb->andWhere('ws.id = :workflowStep')->setParameter('workflowStep', $filters['currentWorkflowStep']);
        }
        if (!empty($filters['roadAxis'])) {
            $qb->andWhere('ra.id = :roadAxis')->setParameter('roadAxis', $filters['roadAxis']);
        }
        if (!empty($filters['location'])) {
            $qb->andWhere('l.id = :location')->setParameter('location', $filters['location']);
        }
        if (!empty($filters['involvedCompany'])) {
            $qb->andWhere('ic.id = :involvedCompany')->setParameter('involvedCompany', $filters['involvedCompany']);
        }

        if (array_key_exists('isSensitive', $filters) && $filters['isSensitive'] !== null && $filters['isSensitive'] !== '') {
            $qb->andWhere('c.isSensitive = :isSensitive')->setParameter('isSensitive', filter_var($filters['isSensitive'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
        }
        if (array_key_exists('isReceivable', $filters) && $filters['isReceivable'] !== null && $filters['isReceivable'] !== '') {
            $qb->andWhere('c.isReceivable = :isReceivable')->setParameter('isReceivable', filter_var($filters['isReceivable'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
        }
        if (array_key_exists('closed', $filters) && $filters['closed'] !== null && $filters['closed'] !== '') {
            $qb->andWhere('c.closed = :closed')->setParameter('closed', filter_var($filters['closed'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
        }

        $this->applyDateFilter($qb, $filters, 'declarationDate', 'c.declarationDate');
        $this->applyDateFilter($qb, $filters, 'incidentDate', 'c.incidentDate');
        $this->applyDateFilter($qb, $filters, 'closureDate', 'c.closureDate');
    }

    private function applyDateFilter(QueryBuilder $qb, array $filters, string $filterKey, string $fieldExpr): void
    {
        $range = $filters[$filterKey] ?? null;
        if (!is_array($range)) {
            return;
        }

        $after = $range['after'] ?? null;
        $before = $range['before'] ?? null;

        if ($after) {
            $qb->andWhere(sprintf('%s >= :%s_after', $fieldExpr, $filterKey))
                ->setParameter($filterKey . '_after', new \DateTimeImmutable($after));
        }

        if ($before) {
            $qb->andWhere(sprintf('%s <= :%s_before', $fieldExpr, $filterKey))
                ->setParameter($filterKey . '_before', (new \DateTimeImmutable($before))->setTime(23, 59, 59));
        }
    }

    private function applyOrdering(QueryBuilder $qb, array $filters): void
    {
        $order = $filters['order'] ?? null;
        if (is_array($order) && !empty($order)) {
            foreach ($order as $field => $direction) {
                $dir = strtoupper((string) $direction) === 'ASC' ? 'ASC' : 'DESC';
                if ($field === 'declarationDate') {
                    $qb->addOrderBy('c.declarationDate', $dir);
                } elseif ($field === 'incidentDate') {
                    $qb->addOrderBy('c.incidentDate', $dir);
                } elseif ($field === 'closureDate') {
                    $qb->addOrderBy('c.closureDate', $dir);
                }
            }
            return;
        }

        $qb->addOrderBy('c.declarationDate', 'DESC');
    }

    private function applyPagination(QueryBuilder $qb): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            $qb->setMaxResults(30);
            return;
        }

        $itemsPerPage = (int) $request->query->get('itemsPerPage', 30);
        if ($itemsPerPage < 1) {
            $itemsPerPage = 30;
        }
        if ($itemsPerPage > 200) {
            $itemsPerPage = 200;
        }

        $page = (int) $request->query->get('page', 1);
        if ($page < 1) {
            $page = 1;
        }

        $qb->setFirstResult(($page - 1) * $itemsPerPage);
        $qb->setMaxResults($itemsPerPage);
    }
}
