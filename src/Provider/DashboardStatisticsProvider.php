<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\DashboardStatistics;
use App\Constant\WorkflowStepName;
use App\Entity\Complaint;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;
use Psr\Log\LoggerInterface;

final class DashboardStatisticsProvider implements ProviderInterface
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation->getName() !== 'get_dashboard_statistics') {
            return null;
        }

        $stats = new DashboardStatistics();

        $filters = $context['filters'] ?? [];
        $locationId = $filters['locationId'] ?? null;
        $roadAxisId = $filters['roadAxisId'] ?? null;
        $complaintTypeId = $filters['complaintTypeId'] ?? null;
        $startDate = $filters['declarationDate']['after'] ?? null;
        $endDate = $filters['declarationDate']['before'] ?? null;

        try {
            $applyCommonFilters = $this->getClosure($roadAxisId, $locationId, $complaintTypeId, $startDate, $endDate);
            $finalClosedStepNames = [WorkflowStepName::CLOSED, WorkflowStepName::ESCALATED_JUSTICE, WorkflowStepName::NON_RECEIVABLE];

            $qb1 = $this->entityManager->createQueryBuilder()
                ->select('COALESCE(wsuic.title, ws.name) AS status, COUNT(c.id) AS count')
                ->from(Complaint::class, 'c')
                ->join('c.currentWorkflowStep', 'ws')
                ->leftJoin('ws.uiConfiguration', 'wsuic');
            $applyCommonFilters($qb1, 'c');
            $qb1->groupBy('status');
            $stats->complaintsByStatus = $qb1->getQuery()->getResult();

            $qb2 = $this->entityManager->createQueryBuilder()
                ->select('gt.value AS type, COUNT(c.id) AS count')
                ->from(Complaint::class, 'c')
                ->join('c.complaintType', 'gt');
            $applyCommonFilters($qb2, 'c');
            $qb2->groupBy('gt.value');
            $stats->complaintsByType = $qb2->getQuery()->getResult();

            $qb5 = $this->entityManager->createQueryBuilder()
                ->select('c.isSensitive, COUNT(c.id) AS count')
                ->from(Complaint::class, 'c');
            $applyCommonFilters($qb5, 'c');
            $qb5->groupBy('c.isSensitive');
            $stats->complaintsBySensitivity = $qb5->getQuery()->getResult();

            $statsQb = $this->entityManager->createQueryBuilder()
                ->select(
                    'c.isSensitive',
                    'CASE WHEN ws.name IN (:finalClosedNames) THEN \'closed\' ELSE \'open\' END AS status',
                    'COUNT(c.id) AS count'
                )
                ->from(Complaint::class, 'c')
                ->join('c.currentWorkflowStep', 'ws')
                ->setParameter('finalClosedNames', $finalClosedStepNames);

            $applyCommonFilters($statsQb, 'c');
            $statsQb->groupBy('c.isSensitive', 'status');
            $results = $statsQb->getQuery()->getResult();

            $stats->complaintStats = [
                'general' => ['total' => 0, 'open' => 0, 'closed' => 0],
                'sensitive' => ['total' => 0, 'open' => 0, 'closed' => 0],
            ];

            foreach ($results as $row) {
                $category = $row['isSensitive'] ? 'sensitive' : 'general';
                $status = $row['status'];
                $count = (int)$row['count'];

                $stats->complaintStats[$category][$status] = $count;
                $stats->complaintStats[$category]['total'] += $count;
            }

            $stats->totalComplaints = $stats->complaintStats['general']['total'] + $stats->complaintStats['sensitive']['total'];
            $stats->openComplaints = $stats->complaintStats['general']['open'] + $stats->complaintStats['sensitive']['open'];
            $stats->totalSensitiveComplaints = $stats->complaintStats['sensitive']['total'];
            $stats->openSensitiveComplaints = $stats->complaintStats['sensitive']['open'];
            $stats->closedSensitiveComplaints = $stats->complaintStats['sensitive']['closed'];

            $stats->averageResolutionTimeDays = $this->calculateAverageResolutionTime($roadAxisId, $locationId, $complaintTypeId, $startDate, $endDate);
            $stats->complaintsDeclaredMonthly = $this->getComplaintsDeclaredMonthly($locationId, $complaintTypeId);

        } catch (\Exception $e) {
            $this->logger->error('Error fetching dashboard statistics: ' . $e->getMessage(), ['exception' => $e]);
            return new DashboardStatistics();
        }

        return $stats;
    }

    private function calculateAverageResolutionTime(?string $roadAxisId, ?string $locationId, ?string $complaintTypeId, ?string $startDate, ?string $endDate): ?float
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('c.declarationDate, c.closureDate')
            ->from(Complaint::class, 'c')
            ->where('c.closureDate IS NOT NULL');

        $applyCommonFiltersForAverage = $this->getClosure($roadAxisId, $locationId, $complaintTypeId, $startDate, $endDate);
        $applyCommonFiltersForAverage($qb, 'c');

        $closedComplaintsData = $qb->getQuery()->getResult();

        if (empty($closedComplaintsData)) {
            return null;
        }

        $totalDays = 0;
        $count = 0;

        foreach ($closedComplaintsData as $data) {
            if ($data['declarationDate'] && $data['closureDate']) {
                $interval = $data['declarationDate']->diff($data['closureDate']);
                $totalDays += $interval->days;
                $count++;
            }
        }

        return $count > 0 ? (float)($totalDays / $count) : null;
    }

    /**
     * Refactored for performance: executes 1 query instead of 12.
     */
    private function getComplaintsDeclaredMonthly(?string $locationId, ?string $complaintTypeId): array
    {
        $now = new \DateTimeImmutable();
        $twelveMonthsAgo = $now->modify('-11 months')->modify('first day of this month')->setTime(0, 0, 0);

        // This uses a database-specific function, assuming MySQL/PostgreSQL.
        // For full portability, you might need platform-specific logic.
        $monthExpression = new Func('SUBSTRING', 'c.declarationDate', 1, 7);

        $qb = $this->entityManager->createQueryBuilder()
            ->select($monthExpression . " as month_key", "COUNT(c.id) as count")
            ->from(Complaint::class, 'c')
            ->where('c.declarationDate >= :startDate')
            ->setParameter('startDate', $twelveMonthsAgo)
            ->groupBy('month_key')
            ->orderBy('month_key', 'ASC');

        if ($locationId) {
            $qb
                ->andWhere('c.location = :locationIdFiltered')
                ->setParameter('locationIdFiltered', $locationId);
        }
        if ($complaintTypeId) {
            $qb
                ->andWhere('c.complaintType = :complaintTypeIdFiltered')
                ->setParameter('complaintTypeIdFiltered', $complaintTypeId);
        }

        $results = $qb->getQuery()->getResult();
        $resultsByMonth = array_column($results, 'count', 'month_key');

        $data = [];
        $dateCursor = $twelveMonthsAgo;

        while ($dateCursor <= $now) {
            $monthKey = $dateCursor->format('Y-m');
            $data[] = [
                'month' => $monthKey,
                'count' => (int)($resultsByMonth[$monthKey] ?? 0)
            ];
            $dateCursor = $dateCursor->modify('+1 month');
        }

        return $data;
    }

    public function getClosure(mixed $roadAxisId, mixed $locationId, mixed $complaintTypeId, mixed $startDate, mixed $endDate): \Closure
    {
        return function (QueryBuilder $qb, string $alias) use ($roadAxisId, $locationId, $complaintTypeId, $startDate, $endDate) {
            if ($roadAxisId) {
                $qb
                    ->andWhere(sprintf('%s.roadAxis = :roadAxisId', $alias))
                    ->setParameter('roadAxisId', $roadAxisId);
            }
            if ($locationId) {
                $qb
                    ->andWhere(sprintf('%s.location = :locationId', $alias))
                    ->setParameter('locationId', $locationId);
            }
            if ($complaintTypeId) {
                $qb
                    ->andWhere(sprintf('%s.complaintType = :complaintTypeId', $alias))
                    ->setParameter('complaintTypeId', $complaintTypeId);
            }
            if ($startDate) {
                $qb
                    ->andWhere(sprintf('%s.declarationDate >= :startDate', $alias))
                    ->setParameter('startDate', new \DateTimeImmutable($startDate));
            }
            if ($endDate) {
                $qb
                    ->andWhere(sprintf('%s.declarationDate <= :endDate', $alias))
                    ->setParameter('endDate', (new \DateTimeImmutable($endDate))->setTime(23, 59, 59));
            }
        };
    }
}
