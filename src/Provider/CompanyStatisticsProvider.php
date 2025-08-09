<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Company\CompanyStatisticsDto;
use App\Repository\ComplaintRepository;
use Doctrine\ORM\QueryBuilder;

final readonly class CompanyStatisticsProvider implements ProviderInterface
{
    public function __construct(
        private ComplaintRepository $complaintRepository
    )
    {
    }

    /**
     * @return array<CompanyStatisticsDto>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $filters = $context['filters'] ?? [];

        $qb = $this->complaintRepository->createQueryBuilder('p')
            ->select(sprintf('NEW %s(c.id, c.name, COUNT(p.id))', CompanyStatisticsDto::class))
            ->join('p.involvedCompany', 'c')
            ->where('p.involvedCompany IS NOT NULL');

        $this->applyFilters($qb, $filters);

        $qb->groupBy('c.id, c.name')
            ->orderBy('c.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    private function applyFilters(QueryBuilder $qb, array $filters): void
    {
        if (!empty($filters['workflowStepId'])) {
            $qb->andWhere('p.currentWorkflowStep = :workflowStepId')
                ->setParameter('workflowStepId', $filters['workflowStepId']);
        }

        if (!empty($filters['involvedCompany'])) {
            $qb->andWhere('p.involvedCompany = :involvedCompanyId')
                ->setParameter('involvedCompanyId', $filters['involvedCompany']);
        }

        if (!empty($filters['complaintTypeId'])) {
            $qb
                ->andWhere('ct.id = :complaintTypeId')
                ->leftJoin('p.complaintType', 'ct')
                ->setParameter('complaintTypeId', $filters['complaintTypeId']);
        }

        if (!empty($filters['declarationDate']['after'])) {
            $qb->andWhere('p.declarationDate >= :startDate')
                ->setParameter('startDate', new \DateTimeImmutable($filters['declarationDate']['after']));
        }
        if (!empty($filters['declarationDate']['before'])) {
            $qb->andWhere('p.declarationDate <= :endDate')
                ->setParameter('endDate', (new \DateTimeImmutable($filters['declarationDate']['before']))->setTime(23, 59, 59));
        }
    }
}
