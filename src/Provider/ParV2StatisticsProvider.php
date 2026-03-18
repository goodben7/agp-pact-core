<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\ParV2Statistics;
use App\Entity\ParV2;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Psr\Log\LoggerInterface;

final readonly class ParV2StatisticsProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation->getName() !== 'get_parv2_statistics') {
            return null;
        }

        $stats = new ParV2Statistics();
        $filters = $context['filters'] ?? [];

        $province = $filters['province'] ?? null;
        $axeRoutier = $filters['axeRoutier'] ?? null;
        $lieuActifAffecte = $filters['lieuActifAffecte'] ?? null;
        $submittedBy = $filters['submittedBy'] ?? null;
        $koboStatus = $filters['koboStatus'] ?? null;
        $sexeChefMenage = $filters['sexeChefMenage'] ?? null;
        $typeActifAffect = $filters['typeActifAffect'] ?? null;

        $dateInventaireAfter = $filters['dateInventaire']['after'] ?? null;
        $dateInventaireBefore = $filters['dateInventaire']['before'] ?? null;

        $submissionTimeAfter = $filters['submissionTime']['after'] ?? null;
        $submissionTimeBefore = $filters['submissionTime']['before'] ?? null;

        $createdAtAfter = $filters['createdAt']['after'] ?? null;
        $createdAtBefore = $filters['createdAt']['before'] ?? null;

        try {
            $applyFilters = $this->getFiltersClosure(
                $province,
                $axeRoutier,
                $lieuActifAffecte,
                $submittedBy,
                $koboStatus,
                $sexeChefMenage,
                $typeActifAffect,
                $dateInventaireAfter,
                $dateInventaireBefore,
                $submissionTimeAfter,
                $submissionTimeBefore,
                $createdAtAfter,
                $createdAtBefore
            );

            $this->populateTotals($stats, $applyFilters);
            $this->populateAverageAge($stats, $applyFilters);
            $this->populateDistributions($stats, $applyFilters);
            $this->populateMonthlyInventories($stats, $applyFilters);
        } catch (\Throwable $e) {
            $this->logger->error('Error fetching ParV2 statistics: ' . $e->getMessage(), ['exception' => $e]);
            return new ParV2Statistics();
        }

        return $stats;
    }

    private function populateTotals(ParV2Statistics $stats, \Closure $applyFilters): void
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select(
                'COUNT(p.id) as total',
                "SUM(CASE WHEN p.gpsActif IS NOT NULL AND p.gpsActif <> '' THEN 1 ELSE 0 END) as withGps",
                "SUM(CASE WHEN p.photo1 IS NOT NULL AND p.photo1 <> '' THEN 1 ELSE 0 END) as withPhoto1",
                "SUM(CASE WHEN p.photo2 IS NOT NULL AND p.photo2 <> '' THEN 1 ELSE 0 END) as withPhoto2",
                'SUM(CASE WHEN p.attachments IS NOT NULL THEN 1 ELSE 0 END) as withAttachments'
            )
            ->from(ParV2::class, 'p');

        $applyFilters($qb, 'p');

        $row = $qb->getQuery()->getSingleResult();

        $stats->totalRecords = (int) ($row['total'] ?? 0);
        $stats->withGps = (int) ($row['withGps'] ?? 0);
        $stats->withPhoto1 = (int) ($row['withPhoto1'] ?? 0);
        $stats->withPhoto2 = (int) ($row['withPhoto2'] ?? 0);
        $stats->withAttachments = (int) ($row['withAttachments'] ?? 0);
    }

    private function populateAverageAge(ParV2Statistics $stats, \Closure $applyFilters): void
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('AVG(p.ageChefMenage) as avg_age')
            ->from(ParV2::class, 'p')
            ->where('p.ageChefMenage IS NOT NULL');

        $applyFilters($qb, 'p');

        $avg = $qb->getQuery()->getSingleScalarResult();
        $stats->averageAgeChefMenage = $avg !== null ? (float) $avg : 0.0;
    }

    private function populateDistributions(ParV2Statistics $stats, \Closure $applyFilters): void
    {
        $stats->byProvince = $this->groupCount($applyFilters, 'p.province', 'province');
        $stats->byAxeRoutier = $this->groupCount($applyFilters, 'p.axeRoutier', 'axeRoutier');
        $stats->byLieuActifAffecte = $this->groupCount($applyFilters, 'p.lieuActifAffecte', 'lieuActifAffecte');
        $stats->byKoboStatus = $this->groupCount($applyFilters, 'p.koboStatus', 'koboStatus');
        $stats->bySubmittedBy = $this->groupCount($applyFilters, 'p.submittedBy', 'submittedBy');
        $stats->byTypeActifAffect = $this->groupCount($applyFilters, 'p.typeActifAffect', 'typeActifAffect');
        $stats->byGenderChefMenage = $this->groupCount($applyFilters, 'p.sexeChefMenage', 'sexeChefMenage');
    }

    private function groupCount(\Closure $applyFilters, string $fieldExpr, string $keyName): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select(sprintf('%s as k, COUNT(p.id) as count', $fieldExpr))
            ->from(ParV2::class, 'p')
            ->where(sprintf('%s IS NOT NULL', $fieldExpr));

        $applyFilters($qb, 'p');
        $qb->groupBy('k');

        $results = $qb->getQuery()->getResult();

        $out = [];
        foreach ($results as $row) {
            $out[] = [
                $keyName => $row['k'],
                'count' => (int) $row['count'],
            ];
        }

        return $out;
    }

    private function populateMonthlyInventories(ParV2Statistics $stats, \Closure $applyFilters): void
    {
        $twelveMonthsAgo = (new \DateTimeImmutable())
            ->modify('-11 months')
            ->modify('first day of this month')
            ->setTime(0, 0, 0);

        $qb = $this->entityManager->createQueryBuilder()
            ->select('YEAR(p.dateInventaire) as year, MONTH(p.dateInventaire) as month, COUNT(p.id) as count')
            ->from(ParV2::class, 'p')
            ->where('p.dateInventaire IS NOT NULL')
            ->andWhere('p.dateInventaire >= :startDate')
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
                $data[$monthKey]['count'] = (int) $row['count'];
            }
        }

        $stats->inventoriesMonthly = array_values($data);
    }

    private function getFiltersClosure(
        ?string $province,
        ?string $axeRoutier,
        ?string $lieuActifAffecte,
        ?string $submittedBy,
        ?string $koboStatus,
        ?string $sexeChefMenage,
        ?string $typeActifAffect,
        ?string $dateInventaireAfter,
        ?string $dateInventaireBefore,
        ?string $submissionTimeAfter,
        ?string $submissionTimeBefore,
        ?string $createdAtAfter,
        ?string $createdAtBefore
    ): \Closure {
        return function (QueryBuilder $qb, string $alias) use (
            $province,
            $axeRoutier,
            $lieuActifAffecte,
            $submittedBy,
            $koboStatus,
            $sexeChefMenage,
            $typeActifAffect,
            $dateInventaireAfter,
            $dateInventaireBefore,
            $submissionTimeAfter,
            $submissionTimeBefore,
            $createdAtAfter,
            $createdAtBefore
        ) {
            if (!empty($province)) {
                $qb->andWhere(sprintf('%s.province = :province', $alias))->setParameter('province', $province);
            }
            if (!empty($axeRoutier)) {
                $qb->andWhere(sprintf('%s.axeRoutier = :axeRoutier', $alias))->setParameter('axeRoutier', $axeRoutier);
            }
            if (!empty($lieuActifAffecte)) {
                $qb->andWhere(sprintf('%s.lieuActifAffecte = :lieuActifAffecte', $alias))->setParameter('lieuActifAffecte', $lieuActifAffecte);
            }
            if (!empty($submittedBy)) {
                $qb->andWhere(sprintf('%s.submittedBy = :submittedBy', $alias))->setParameter('submittedBy', $submittedBy);
            }
            if (!empty($koboStatus)) {
                $qb->andWhere(sprintf('%s.koboStatus = :koboStatus', $alias))->setParameter('koboStatus', $koboStatus);
            }
            if (!empty($sexeChefMenage)) {
                $qb->andWhere(sprintf('%s.sexeChefMenage = :sexeChefMenage', $alias))->setParameter('sexeChefMenage', $sexeChefMenage);
            }
            if (!empty($typeActifAffect)) {
                $qb->andWhere(sprintf('%s.typeActifAffect = :typeActifAffect', $alias))->setParameter('typeActifAffect', $typeActifAffect);
            }

            if (!empty($dateInventaireAfter)) {
                $qb->andWhere(sprintf('%s.dateInventaire >= :dateInventaireAfter', $alias))
                    ->setParameter('dateInventaireAfter', new \DateTimeImmutable($dateInventaireAfter));
            }
            if (!empty($dateInventaireBefore)) {
                $qb->andWhere(sprintf('%s.dateInventaire <= :dateInventaireBefore', $alias))
                    ->setParameter('dateInventaireBefore', (new \DateTimeImmutable($dateInventaireBefore))->setTime(23, 59, 59));
            }

            if (!empty($submissionTimeAfter)) {
                $qb->andWhere(sprintf('%s.submissionTime >= :submissionTimeAfter', $alias))
                    ->setParameter('submissionTimeAfter', new \DateTimeImmutable($submissionTimeAfter));
            }
            if (!empty($submissionTimeBefore)) {
                $qb->andWhere(sprintf('%s.submissionTime <= :submissionTimeBefore', $alias))
                    ->setParameter('submissionTimeBefore', (new \DateTimeImmutable($submissionTimeBefore))->setTime(23, 59, 59));
            }

            if (!empty($createdAtAfter)) {
                $qb->andWhere(sprintf('%s.createdAt >= :createdAtAfter', $alias))
                    ->setParameter('createdAtAfter', new \DateTimeImmutable($createdAtAfter));
            }
            if (!empty($createdAtBefore)) {
                $qb->andWhere(sprintf('%s.createdAt <= :createdAtBefore', $alias))
                    ->setParameter('createdAtBefore', (new \DateTimeImmutable($createdAtBefore))->setTime(23, 59, 59));
            }
        };
    }
}

