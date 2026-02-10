<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\DashboardStatistics;
use App\Entity\Complaint;
use App\Repository\LocationRepository;
use App\Repository\RoadAxisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Psr\Log\LoggerInterface;
use App\Constant\GeneralParameterPersonType;

final readonly class DashboardStatisticsProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface        $logger,
        private LocationRepository     $locationRepository,
        private RoadAxisRepository     $roadAxisRepository
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation->getName() !== 'get_dashboard_statistics') {
            return null;
        }
    
        $stats = new DashboardStatistics();
    
        $filters = $context['filters'] ?? [];
        $this->logger->info('Dashboard filters received: ' . json_encode($filters));
    
        $locationId = $filters['location'] ?? null;
        $involvedCompanyId = $filters['involvedCompany'] ?? null;
        $roadAxisId = $filters['roadAxisId'] ?? null;
        $complaintTypeId = $filters['complaintTypeId'] ?? null;
        $startDate = $filters['incidentDate']['after'] ?? null;
        $endDate = $filters['incidentDate']['before'] ?? null;
    
        try {
            $locationIds = $this->getLocationIdsForFilter($locationId, $roadAxisId);
            $this->logger->info('Location IDs for filter: ' . json_encode($locationIds));
            
            // Vérification simple du nombre total de plaintes
            $totalComplaintsQb = $this->entityManager->createQueryBuilder()
                ->select('COUNT(c.id)')
                ->from(Complaint::class, 'c');
            $totalComplaints = $totalComplaintsQb->getQuery()->getSingleScalarResult();
            $this->logger->info('Total complaints in database: ' . $totalComplaints);
    
            // Utiliser des filtres sans date pour les statistiques principales
            $applyCommonFilters = $this->getClosure($locationIds, $complaintTypeId, null, null, $involvedCompanyId);
            
            // Utiliser les filtres avec date seulement si des dates sont spécifiées
            $applyDateFilters = $this->getClosure($locationIds, $complaintTypeId, $startDate, $endDate, $involvedCompanyId);
    
            $this->populateComplaintCounts($stats, $applyCommonFilters);
            $this->populateComplaintsByStatus($stats, $applyCommonFilters);
            $this->populateComplaintsByType($stats, $applyCommonFilters);
            $this->populateAverageResolutionTime($stats, $applyCommonFilters);
            $this->populateComplaintLifecycleStats($stats, $applyCommonFilters);

    
            $monthlyFiltersClosure = $this->getClosure($locationIds, $complaintTypeId, null, null, $involvedCompanyId);
            $this->populateComplaintsDeclaredMonthly($stats, $monthlyFiltersClosure);
        } catch (\Exception $e) {
            $this->logger->error('Error fetching dashboard statistics: ' . $e->getMessage(), ['exception' => $e]);
            return new DashboardStatistics();
        }
    
        return $stats;
    }

    private function populateComplaintCounts(DashboardStatistics $stats, \Closure $applyCommonFilters): void
    {
        $statsQb = $this->entityManager->createQueryBuilder()
            ->select(
                'c.isSensitive',
                "CASE WHEN c.isReceivable = false THEN 'rejected' WHEN c.closed = true THEN 'closed' ELSE 'open' END AS status",
                'COUNT(c.id) AS count'
            )
            ->from(Complaint::class, 'c');
        
        $applyCommonFilters($statsQb, 'c');
        $statsQb->groupBy('c.isSensitive', 'status');
        
        $sql = $statsQb->getQuery()->getSQL();
        $this->logger->info('SQL Query for complaint counts: ' . $sql);
        
        $results = $statsQb->getQuery()->getResult();
        $this->logger->info('Results for complaint counts: ' . json_encode($results));
    
        foreach ($results as $row) {
            // Gestion des 3 niveaux de sensibilité
            if ($row['isSensitive'] === null) {
                $categoryDto = $stats->general;
            } elseif ($row['isSensitive'] === false) {
                $categoryDto = $stats->sensitive;
            } else { // $row['isSensitive'] === true
                $categoryDto = $stats->hypersensitive;
            }
            
            $status = $row['status'];
            $count = (int)$row['count'];
    
            if ($status === 'open') {
                $categoryDto->openComplaints = $count;
            } elseif ($status === 'closed') {
                $categoryDto->closedComplaints = $count;
            } elseif ($status === 'rejected') {
                $categoryDto->rejectedComplaints = $count;
            }
            $categoryDto->totalComplaints += $count;
        }
    }

    private function populateComplaintsByStatus(DashboardStatistics $stats, \Closure $applyCommonFilters): void
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('c.isSensitive, COUNT(c.id) AS count, COALESCE(wsuic.title, ws.name, \'Non défini\') AS status')
            ->from(Complaint::class, 'c')
            ->leftJoin('c.currentWorkflowStep', 'ws')
            ->leftJoin('ws.uiConfiguration', 'wsuic');
        $applyCommonFilters($qb, 'c');
        $qb->groupBy('c.isSensitive', 'status');
        $results = $qb->getQuery()->getResult();

        foreach ($results as $row) {
            // Gestion des 3 niveaux de sensibilité
            if ($row['isSensitive'] === null) {
                $categoryDto = $stats->general;
            } elseif ($row['isSensitive'] === false) {
                $categoryDto = $stats->sensitive;
            } else { // $row['isSensitive'] === true
                $categoryDto = $stats->hypersensitive;
            }
            
            $categoryDto->complaintsByStatus[] = ['status' => $row['status'], 'count' => (int)$row['count']];
        }
    }

    private function populateComplaintsByType(DashboardStatistics $stats, \Closure $applyCommonFilters): void
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('c.isSensitive, gt.value AS type, COUNT(c.id) AS count')
            ->from(Complaint::class, 'c')
            ->join('c.complaintType', 'gt')
            ->where('gt.category = :personTypeCategory')
            ->setParameter('personTypeCategory', GeneralParameterPersonType::CATEGORY_PERSON_TYPE);
        
        $applyCommonFilters($qb, 'c');
        $qb->groupBy('c.isSensitive', 'type');
        
        // Ajout de logging pour diagnostiquer
        $sql = $qb->getQuery()->getSQL();
        $this->logger->info('SQL Query for complaint types: ' . $sql);
        
        $results = $qb->getQuery()->getResult();
        $this->logger->info('Results for complaint types: ' . json_encode($results));
    
        foreach ($results as $row) {
            // Gestion des 3 niveaux de sensibilité
            if ($row['isSensitive'] === null) {
                $categoryDto = $stats->general;
            } elseif ($row['isSensitive'] === false) {
                $categoryDto = $stats->sensitive;
            } else { // $row['isSensitive'] === true
                $categoryDto = $stats->hypersensitive;
            }
            
            $categoryDto->complaintsByType[] = ['type' => $row['type'], 'count' => (int)$row['count']];
        }
    }

    private function populateAverageResolutionTime(DashboardStatistics $stats, \Closure $applyCommonFilters): void
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('c.isSensitive, AVG(DATE_DIFF(c.closureDate, c.incidentDate)) as avg_days')
            ->from(Complaint::class, 'c')
            ->where('c.closureDate IS NOT NULL')
            ->andWhere('c.closed = :isClosed')
            ->setParameter('isClosed', true);
        $applyCommonFilters($qb, 'c');
        $qb->groupBy('c.isSensitive');
        $results = $qb->getQuery()->getResult();

        foreach ($results as $row) {
            if ($row['avg_days'] === null) continue;
            
            // Gestion des 3 niveaux de sensibilité
            if ($row['isSensitive'] === null) {
                $categoryDto = $stats->general;
            } elseif ($row['isSensitive'] === false) {
                $categoryDto = $stats->sensitive;
            } else { // $row['isSensitive'] === true
                $categoryDto = $stats->hypersensitive;
            }
            
            $categoryDto->averageResolutionTimeDays = (float)$row['avg_days'];
        }
    }

    private function populateComplaintsDeclaredMonthly(DashboardStatistics $stats, \Closure $applyMonthlyFilters): void
    {
        $twelveMonthsAgo = (new \DateTimeImmutable())->modify('-11 months')->modify('first day of this month')->setTime(0, 0, 0);

        $qb = $this->entityManager->createQueryBuilder()
            ->select('c.isSensitive, YEAR(c.incidentDate) as year, MONTH(c.incidentDate) as month, COUNT(c.id) as count')
            ->from(Complaint::class, 'c')
            ->where('c.incidentDate >= :startDate')
            ->setParameter('startDate', $twelveMonthsAgo);
        $applyMonthlyFilters($qb, 'c');
        $qb->groupBy('c.isSensitive', 'year', 'month')->orderBy('year, month');
        $results = $qb->getQuery()->getResult();

        $data = ['general' => [], 'sensitive' => [], 'hypersensitive' => []];
        $now = new \DateTimeImmutable();
        for ($i = 11; $i >= 0; $i--) {
            $monthKey = $now->modify("-{$i} months")->format('Y-m');
            $data['general'][$monthKey] = ['month' => $monthKey, 'count' => 0];
            $data['sensitive'][$monthKey] = ['month' => $monthKey, 'count' => 0];
            $data['hypersensitive'][$monthKey] = ['month' => $monthKey, 'count' => 0];
        }

        foreach ($results as $row) {
            // Gestion des 3 niveaux de sensibilité
            if ($row['isSensitive'] === null) {
                $categoryKey = 'general';
            } elseif ($row['isSensitive'] === false) {
                $categoryKey = 'sensitive';
            } else { // $row['isSensitive'] === true
                $categoryKey = 'hypersensitive';
            }
            
            $monthKey = sprintf('%d-%02d', $row['year'], $row['month']);
            if (isset($data[$categoryKey][$monthKey])) {
                $data[$categoryKey][$monthKey]['count'] = (int)$row['count'];
            }
        }

        $stats->general->complaintsDeclaredMonthly = array_values($data['general']);
        $stats->sensitive->complaintsDeclaredMonthly = array_values($data['sensitive']);
        $stats->hypersensitive->complaintsDeclaredMonthly = array_values($data['hypersensitive']);
    }

    private function getClosure(?array $locationIds, ?string $complaintTypeId, ?string $startDate, ?string $endDate, ?string $involvedCompanyId): \Closure
    {
        return function (QueryBuilder $qb, string $alias) use ($locationIds, $complaintTypeId, $startDate, $endDate, $involvedCompanyId) {
            if ($locationIds !== null && !empty($locationIds)) {
                $qb->andWhere($qb->expr()->in(sprintf('%s.location', $alias), ':locationIds'))
                    ->setParameter('locationIds', $locationIds);
            }
    
            if (!empty($complaintTypeId)) {
                $qb->andWhere(sprintf('%s.complaintType = :complaintTypeId', $alias))->setParameter('complaintTypeId', $complaintTypeId);
            }
            if (!empty($startDate)) {
                $qb->andWhere(sprintf('%s.incidentDate >= :startDate', $alias))->setParameter('startDate', new \DateTimeImmutable($startDate));
            }
            if (!empty($endDate)) {
                $qb->andWhere(sprintf('%s.incidentDate <= :endDate', $alias))->setParameter('endDate', (new \DateTimeImmutable($endDate))->setTime(23, 59, 59));
            }
            if (!empty($involvedCompanyId)) {
                $qb->andWhere(sprintf('%s.involvedCompany = :involvedCompanyId', $alias))->setParameter('involvedCompanyId', $involvedCompanyId);
            }
        };
    }

    private function getLocationIdsForFilter(?string $locationId, ?string $roadAxisId): ?array
    {
        if (empty($locationId) && empty($roadAxisId)) {
            return null;
        }

        $locationIds = [];

        if (!empty($locationId)) {
            $locationIds = array_merge($locationIds, $this->getDescendantLocationIds($locationId));
        }

        if (!empty($roadAxisId)) {
            $roadAxis = $this->roadAxisRepository->find($roadAxisId);
            if ($roadAxis) {
                foreach ($roadAxis->getTraversedLocations() as $loc) {
                    $locationIds[] = $loc->getId();
                }
                foreach ($roadAxis->getProvince() as $loc) {
                    $locationIds[] = $loc->getId();
                }
                foreach ($roadAxis->getTerritory() as $loc) {
                    $locationIds[] = $loc->getId();
                }
                foreach ($roadAxis->getCommune() as $loc) {
                    $locationIds[] = $loc->getId();
                }
                foreach ($roadAxis->getQuartier() as $loc) {
                    $locationIds[] = $loc->getId();
                }
                foreach ($roadAxis->getCity() as $loc) {
                    $locationIds[] = $loc->getId();
                }
                foreach ($roadAxis->getSecteur() as $loc) {
                    $locationIds[] = $loc->getId();
                }
            }
        }

        return array_unique($locationIds);
    }

    private function getDescendantLocationIds(string $locationId): array
    {
        $descendantIds = [$locationId];
        $children = $this->locationRepository->findBy(['parent' => $locationId]);

        foreach ($children as $child) {
            $descendantIds = array_merge($descendantIds, $this->getDescendantLocationIds($child->getId()));
        }

        return array_unique($descendantIds);
    }

    private function populateComplaintLifecycleStats(
        DashboardStatistics $stats,
        \Closure $applyCommonFilters
    ): void {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('
                c.isSensitive,
                COUNT(c.id) as total,
                SUM(CASE WHEN c.currentWorkflowStep IS NULL THEN 1 ELSE 0 END) as unprocessed,
                SUM(CASE WHEN c.isReceivable = true AND c.closed = false THEN 1 ELSE 0 END) as validatedInProgress,
                SUM(CASE WHEN c.isReceivable = false THEN 1 ELSE 0 END) as nonValidated,
                SUM(CASE WHEN c.isReceivable = true AND c.closed = true THEN 1 ELSE 0 END) as validatedResolved,
                SUM(CASE WHEN c.isReceivable IS NULL THEN 1 ELSE 0 END) as receivabilityUndefined
            ')
            ->from(Complaint::class, 'c');

        $applyCommonFilters($qb, 'c');
        $qb->groupBy('c.isSensitive');

        $results = $qb->getQuery()->getResult();

        foreach ($results as $row) {
            // mapping sensibilité (tu maîtrises déjà 👌)
            if ($row['isSensitive'] === null) {
                $category = $stats->general;
            } elseif ($row['isSensitive'] === false) {
                $category = $stats->sensitive;
            } else {
                $category = $stats->hypersensitive;
            }

            $category->totalComplaints = (int) $row['total'];
            $category->unprocessedComplaints = (int) $row['unprocessed'];
            $category->validatedInProgressComplaints = (int) $row['validatedInProgress'];
            $category->nonValidatedComplaints = (int) $row['nonValidated'];
            $category->validatedResolvedComplaints = (int) $row['validatedResolved'];
            $category->receivabilityUndefinedComplaints = (int) $row['receivabilityUndefined'];
        }
    }

}
