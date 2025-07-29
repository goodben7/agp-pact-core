<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\DashboardStatistics;
use App\Constant\GeneralParameterPersonType;
use App\Entity\Complaint;
use Doctrine\ORM\EntityManagerInterface;
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
        if ($operation->getName() !== 'get_dashboard_statistics')
            return null;

        $stats = new DashboardStatistics();

        $filters = $context['filters'] ?? [];
        $locationId = $filters['location'] ?? null;
        $involvedCompanyId = $filters['involvedCompany'] ?? null;
        $roadAxisId = $filters['roadAxisId'] ?? null;
        $complaintTypeId = $filters['complaintTypeId'] ?? null;
        $startDate = $filters['declarationDate']['after'] ?? null;
        $endDate = $filters['declarationDate']['before'] ?? null;

        try {
            $applyCommonFilters = $this->getClosure($roadAxisId, $locationId, $complaintTypeId, $startDate, $endDate, $involvedCompanyId);

            $qb1 = $this->entityManager->createQueryBuilder()
                ->select('COUNT(c.id) AS count')
                ->addSelect('COALESCE(wsuic.title, ws.name) AS status')
                ->addSelect('COALESCE(wsuic.title, ws.name) AS HIDDEN status_expr')
                ->from(Complaint::class, 'c')
                ->join('c.currentWorkflowStep', 'ws')
                ->leftJoin('ws.uiConfiguration', 'wsuic');
            $applyCommonFilters($qb1, 'c');
            $qb1->groupBy('status_expr');
            $stats->complaintsByStatus = $qb1->getQuery()->getResult();

            $qb2 = $this->entityManager->createQueryBuilder()
                ->select('gt.value AS type, COUNT(c.id) AS count')
                ->from(Complaint::class, 'c')
                ->join('c.complaintType', 'gt')
                ->andWhere('gt.category = :personTypeCategory')
                ->setParameter('personTypeCategory', GeneralParameterPersonType::CATEGORY_PERSON_TYPE);
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
                    "CASE
                        WHEN c.isReceivable = false THEN 'rejected'
                        WHEN c.closed = true THEN 'closed'
                        ELSE 'open'
                     END AS status",
                    'COUNT(c.id) AS count'
                )
                ->from(Complaint::class, 'c');

            $applyCommonFilters($statsQb, 'c');
            $statsQb->groupBy('c.isSensitive', 'status');
            $results = $statsQb->getQuery()->getResult();

            $stats->complaintStats = [
                'general' => ['total' => 0, 'open' => 0, 'closed' => 0, 'rejected' => 0],
                'sensitive' => ['total' => 0, 'open' => 0, 'closed' => 0, 'rejected' => 0],
            ];

            foreach ($results as $row) {
                $category = $row['isSensitive'] ? 'sensitive' : 'general';
                $status = $row['status'];
                $count = (int)$row['count'];

                if (array_key_exists($status, $stats->complaintStats[$category])) {
                    $stats->complaintStats[$category][$status] = $count;
                    $stats->complaintStats[$category]['total'] += $count;
                }
            }

            $stats->totalComplaints = $stats->complaintStats['general']['total'] + $stats->complaintStats['sensitive']['total'];
            $stats->openComplaints = $stats->complaintStats['general']['open'] + $stats->complaintStats['sensitive']['open'];
            $stats->totalRejectedComplaints = $stats->complaintStats['general']['rejected'] + $stats->complaintStats['sensitive']['rejected'];
            $stats->totalSensitiveComplaints = $stats->complaintStats['sensitive']['total'];
            $stats->openSensitiveComplaints = $stats->complaintStats['sensitive']['open'];
            $stats->closedSensitiveComplaints = $stats->complaintStats['sensitive']['closed'];

            $stats->averageResolutionTimeDays = $this->calculateAverageResolutionTime($roadAxisId, $locationId, $complaintTypeId, $startDate, $endDate, $involvedCompanyId);
            $stats->complaintsDeclaredMonthly = $this->getComplaintsDeclaredMonthly($locationId, $complaintTypeId);

        } catch (\Exception $e) {
            $this->logger->error('Error fetching dashboard statistics: ' . $e->getMessage(), ['exception' => $e]);
            return new DashboardStatistics();
        }

        return $stats;
    }

    private function calculateAverageResolutionTime(?string $roadAxisId, ?string $locationId, ?string $complaintTypeId, ?string $startDate, ?string $endDate, ?string $involvedCompanyId): ?float
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('c.declarationDate, c.closureDate')
            ->from(Complaint::class, 'c')
            ->where('c.closureDate IS NOT NULL')
            ->andWhere('c.closed = :isClosed')
            ->setParameter('isClosed', true);

        $applyCommonFiltersForAverage = $this->getClosure($roadAxisId, $locationId, $complaintTypeId, $startDate, $endDate, $involvedCompanyId);
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


    private function getComplaintsDeclaredMonthly(?string $locationId, ?string $complaintTypeId): array
    {
        $data = [];
        $now = new \DateTimeImmutable();

        for ($i = 11; $i >= 0; $i--) {
            $month = $now->modify("-{$i} months");
            $startOfMonth = $month->modify('first day of this month')->setTime(0, 0, 0);
            $endOfMonth = $month->modify('last day of this month')->setTime(23, 59, 59);

            $qb = $this->entityManager->createQueryBuilder()
                ->select('COUNT(c.id)')
                ->from(Complaint::class, 'c')
                ->where('c.declarationDate BETWEEN :start AND :end')
                ->setParameter('start', $startOfMonth)
                ->setParameter('end', $endOfMonth);

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

            $count = $qb->getQuery()->getSingleScalarResult();

            $data[] = [
                'month' => $month->format('Y-m'),
                'count' => (int)$count
            ];
        }

        return $data;
    }

    public function getClosure(mixed $roadAxisId, mixed $locationId, mixed $complaintTypeId, mixed $startDate, mixed $endDate, mixed $involvedCompanyId): \Closure
    {
        return function (QueryBuilder $qb, string $alias) use ($roadAxisId, $locationId, $complaintTypeId, $startDate, $endDate, $involvedCompanyId) {
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
            if ($involvedCompanyId) {
                $qb
                    ->andWhere(sprintf('%s.involvedCompany = :involvedCompanyId', $alias))
                    ->setParameter('involvedCompanyId', $involvedCompanyId);
            }
        };
    }
}
