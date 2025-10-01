<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\ParStatistics;
use App\Entity\Par;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Psr\Log\LoggerInterface;

final readonly class ParStatisticsProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface        $logger
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation->getName() !== 'get_par_statistics') {
            return null;
        }

        $stats = new ParStatistics();
        $filters = $context['filters'] ?? [];
        $this->logger->info('Par statistics filters received: ' . json_encode($filters));

        $type = $filters['type'] ?? null;
        $province = $filters['province'] ?? null;
        $territory = $filters['territory'] ?? null;
        $village = $filters['village'] ?? null;
        $sexe = $filters['sexe'] ?? null;
        $category = $filters['category'] ?? null;
        $typeLiability = $filters['typeLiability'] ?? null;
        $orientation = $filters['orientation'] ?? null;
        $vulnerabilityType = $filters['vulnerabilityType'] ?? null;
        $vulnerability = $filters['vulnerability'] ?? null;
        $formerPap = $filters['formerPap'] ?? null;
        $startDate = $filters['createdAt']['after'] ?? null;
        $endDate = $filters['createdAt']['before'] ?? null;

        try {
            $applyFilters = $this->getFiltersClosure(
                $type, $province, $territory, $village, $sexe, $category,
                $typeLiability, $orientation, $vulnerabilityType, $vulnerability,
                $formerPap, $startDate, $endDate
            );

            $this->populateParCounts($stats, $applyFilters);
            $this->populateParsByType($stats, $applyFilters);
            $this->populateParsByVulnerability($stats, $applyFilters);
            $this->populateParsByLocation($stats, $applyFilters);
            $this->populateParsByGender($stats, $applyFilters);
            $this->populateAverageAge($stats, $applyFilters);
            $this->populateParsCreatedMonthly($stats, $applyFilters);
            $this->populateCompensationStats($stats, $applyFilters);
        } catch (\Exception $e) {
            $this->logger->error('Error fetching par statistics: ' . $e->getMessage(), ['exception' => $e]);
            return new ParStatistics();
        }

        return $stats;
    }

    private function populateParCounts(ParStatistics $stats, \Closure $applyFilters): void
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p.type, COUNT(p.id) AS count')
            ->from(Par::class, 'p');
        
        $applyFilters($qb, 'p');
        $qb->groupBy('p.type');
        
        $results = $qb->getQuery()->getResult();
        
        foreach ($results as $row) {
            $count = (int)$row['count'];
            $stats->totalPars += $count;
            
            switch ($row['type']) {
                case Par::TYPE_TOMBS:
                    $stats->tombsPars = $count;
                    break;
                case Par::TYPE_OWNER:
                    $stats->ownerPars = $count;
                    break;
                case Par::TYPE_TENANT:
                    $stats->tenantPars = $count;
                    break;
                case Par::TYPE_MINOR:
                    $stats->minorPars = $count;
                    break;
                case Par::TYPE_OTHER:
                    $stats->otherPars = $count;
                    break;
            }
        }
    }

    private function populateParsByType(ParStatistics $stats, \Closure $applyFilters): void
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p.type, COUNT(p.id) AS count')
            ->from(Par::class, 'p');
        
        $applyFilters($qb, 'p');
        $qb->groupBy('p.type');
        
        $results = $qb->getQuery()->getResult();
        
        foreach ($results as $row) {
            $stats->parsByType[] = [
                'type' => $row['type'],
                'count' => (int)$row['count']
            ];
        }
    }

    private function populateParsByVulnerability(ParStatistics $stats, \Closure $applyFilters): void
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p.vulnerability, p.vulnerabilityType, COUNT(p.id) AS count')
            ->from(Par::class, 'p');
        
        $applyFilters($qb, 'p');
        $qb->groupBy('p.vulnerability', 'p.vulnerabilityType');
        
        $results = $qb->getQuery()->getResult();
        
        foreach ($results as $row) {
            if ($row['vulnerability']) {
                $stats->vulnerablePars += (int)$row['count'];
            }
            
            $stats->parsByVulnerability[] = [
                'vulnerability' => $row['vulnerability'],
                'vulnerabilityType' => $row['vulnerabilityType'],
                'count' => (int)$row['count']
            ];
        }
    }

    private function populateParsByLocation(ParStatistics $stats, \Closure $applyFilters): void
    {
        // Par province
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p.province, COUNT(p.id) AS count')
            ->from(Par::class, 'p')
            ->where('p.province IS NOT NULL');
        
        $applyFilters($qb, 'p');
        $qb->groupBy('p.province');
        
        $results = $qb->getQuery()->getResult();
        foreach ($results as $row) {
            $stats->parsByProvince[] = [
                'province' => $row['province'],
                'count' => (int)$row['count']
            ];
        }

        // Par territoire
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p.territory, COUNT(p.id) AS count')
            ->from(Par::class, 'p')
            ->where('p.territory IS NOT NULL');
        
        $applyFilters($qb, 'p');
        $qb->groupBy('p.territory');
        
        $results = $qb->getQuery()->getResult();
        foreach ($results as $row) {
            $stats->parsByTerritory[] = [
                'territory' => $row['territory'],
                'count' => (int)$row['count']
            ];
        }

        // Par village
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p.village, COUNT(p.id) AS count')
            ->from(Par::class, 'p')
            ->where('p.village IS NOT NULL');
        
        $applyFilters($qb, 'p');
        $qb->groupBy('p.village');
        
        $results = $qb->getQuery()->getResult();
        foreach ($results as $row) {
            $stats->parsByVillage[] = [
                'village' => $row['village'],
                'count' => (int)$row['count']
            ];
        }
    }

    private function populateParsByGender(ParStatistics $stats, \Closure $applyFilters): void
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p.sexe, COUNT(p.id) AS count')
            ->from(Par::class, 'p')
            ->where('p.sexe IS NOT NULL');
        
        $applyFilters($qb, 'p');
        $qb->groupBy('p.sexe');
        
        $results = $qb->getQuery()->getResult();
        
        foreach ($results as $row) {
            $stats->parsByGender[] = [
                'gender' => $row['sexe'],
                'count' => (int)$row['count']
            ];
        }
    }

    private function populateAverageAge(ParStatistics $stats, \Closure $applyFilters): void
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('AVG(p.age) as avg_age')
            ->from(Par::class, 'p')
            ->where('p.age IS NOT NULL');
        
        $applyFilters($qb, 'p');
        
        $result = $qb->getQuery()->getSingleScalarResult();
        $stats->averageAge = $result ? (float)$result : 0.0;
    }

    private function populateCompensationStats(ParStatistics $stats, \Closure $applyFilters): void
    {
        // Compter les anciens PAP
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(p.id) AS count')
            ->from(Par::class, 'p')
            ->where('p.formerPap = :formerPap')
            ->setParameter('formerPap', true);
        
        $applyFilters($qb, 'p');
        
        $result = $qb->getQuery()->getSingleScalarResult();
        $stats->formerPapCount = (int)$result;

        // Calculer le montant total de compensation
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p.totalGeneral')
            ->from(Par::class, 'p')
            ->where('p.totalGeneral IS NOT NULL AND p.totalGeneral != \'\'');
        
        $applyFilters($qb, 'p');
        
        $results = $qb->getQuery()->getScalarResult();
        $totalCompensation = 0.0;
        
        foreach ($results as $row) {
            // Convert string to float and add to total
            $value = str_replace(',', '.', $row['totalGeneral']); // Handle possible comma as decimal separator
            $totalCompensation += (float)$value;
        }
        
        $stats->totalCompensationAmount = $totalCompensation;
    }

    private function populateParsCreatedMonthly(ParStatistics $stats, \Closure $applyFilters): void
    {
        $twelveMonthsAgo = (new \DateTimeImmutable())->modify('-11 months')->modify('first day of this month')->setTime(0, 0, 0);

        $qb = $this->entityManager->createQueryBuilder()
            ->select('YEAR(p.createdAt) as year, MONTH(p.createdAt) as month, COUNT(p.id) as count')
            ->from(Par::class, 'p')
            ->where('p.createdAt >= :startDate')
            ->setParameter('startDate', $twelveMonthsAgo);
        
        $applyFilters($qb, 'p');
        $qb->groupBy('year', 'month')->orderBy('year, month');
        
        $results = $qb->getQuery()->getResult();

        $data = [];
        $now = new \DateTimeImmutable();
        for ($i = 11; $i >= 0; $i--) {
            $monthKey = $now->modify("-{$i} months")->format('Y-m');
            $data[$monthKey] = ['month' => $monthKey, 'count' => 0];
        }

        foreach ($results as $row) {
            $monthKey = sprintf('%d-%02d', $row['year'], $row['month']);
            if (isset($data[$monthKey])) {
                $data[$monthKey]['count'] = (int)$row['count'];
            }
        }

        $stats->parsCreatedMonthly = array_values($data);
    }

    private function getFiltersClosure(
        ?string $type, ?string $province, ?string $territory, ?string $village,
        ?string $sexe, ?string $category, ?string $typeLiability, ?string $orientation,
        ?string $vulnerabilityType, ?bool $vulnerability, ?bool $formerPap,
        ?string $startDate, ?string $endDate
    ): \Closure {
        return function (QueryBuilder $qb, string $alias) use (
            $type, $province, $territory, $village, $sexe, $category,
            $typeLiability, $orientation, $vulnerabilityType, $vulnerability,
            $formerPap, $startDate, $endDate
        ) {
            if (!empty($type)) {
                $qb->andWhere(sprintf('%s.type = :type', $alias))->setParameter('type', $type);
            }
            if (!empty($province)) {
                $qb->andWhere(sprintf('%s.province = :province', $alias))->setParameter('province', $province);
            }
            if (!empty($territory)) {
                $qb->andWhere(sprintf('%s.territory = :territory', $alias))->setParameter('territory', $territory);
            }
            if (!empty($village)) {
                $qb->andWhere(sprintf('%s.village = :village', $alias))->setParameter('village', $village);
            }
            if (!empty($sexe)) {
                $qb->andWhere(sprintf('%s.sexe = :sexe', $alias))->setParameter('sexe', $sexe);
            }
            if (!empty($category)) {
                $qb->andWhere(sprintf('%s.category = :category', $alias))->setParameter('category', $category);
            }
            if (!empty($typeLiability)) {
                $qb->andWhere(sprintf('%s.typeLiability = :typeLiability', $alias))->setParameter('typeLiability', $typeLiability);
            }
            if (!empty($orientation)) {
                $qb->andWhere(sprintf('%s.orientation = :orientation', $alias))->setParameter('orientation', $orientation);
            }
            if (!empty($vulnerabilityType)) {
                $qb->andWhere(sprintf('%s.vulnerabilityType = :vulnerabilityType', $alias))->setParameter('vulnerabilityType', $vulnerabilityType);
            }
            if ($vulnerability !== null) {
                $qb->andWhere(sprintf('%s.vulnerability = :vulnerability', $alias))->setParameter('vulnerability', $vulnerability);
            }
            if ($formerPap !== null) {
                $qb->andWhere(sprintf('%s.formerPap = :formerPap', $alias))->setParameter('formerPap', $formerPap);
            }
            if (!empty($startDate)) {
                $qb->andWhere(sprintf('%s.createdAt >= :startDate', $alias))->setParameter('startDate', new \DateTimeImmutable($startDate));
            }
            if (!empty($endDate)) {
                $qb->andWhere(sprintf('%s.createdAt <= :endDate', $alias))->setParameter('endDate', (new \DateTimeImmutable($endDate))->setTime(23, 59, 59));
            }
        };
    }
}