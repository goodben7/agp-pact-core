<?php

namespace App\Service;

use App\Entity\Complaint;
use App\Entity\ComplaintHistory;
use App\Entity\WorkflowStep;
use App\Constant\GeneralParameterReportType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

readonly class ReportDataCollectorService
{
    public function __construct(
        private EntityManagerInterface $em,
    )
    {
    }

    /**
     * Collecte et structure les données pour un type de rapport spécifique, en appliquant les filtres.
     *
     * @param string $reportType A string representing the report type (e.g., 'COMPLAINT_SUMMARY', 'PAP_IMPACT_REPORT').
     * @param array $filters An associative array of filters (e.g., ['locationId' => '...', 'startDate' => '...']).
     * @return array The structured data ready to be passed to a Twig template.
     * @throws \DateMalformedStringException
     */
    public function collectData(string $reportType, array $filters): array
    {
        $reportData = [
            'filters' => $filters,
            'results' => [],
            'summary' => [],
        ];

        switch ($reportType) {
            case GeneralParameterReportType::COMPLAINT_SUMMARY_CODE:
                $reportData['results'] = $this->getComplaintSummaryData($filters);
                $reportData['summary'] = $this->getComplaintSummaryAggregates($filters);
                break;
            /*case GeneralParameterReportType::PAP_IMPACT_REPORT_CODE:
                $reportData['results'] = $this->getPapImpactData($filters);
                $reportData['summary'] = $this->getPapImpactAggregates($filters);
                break;*/
            case GeneralParameterReportType::WORKFLOW_PERFORMANCE_CODE:
                $reportData['results'] = $this->getWorkflowPerformanceData($filters);
                $reportData['summary'] = $this->getWorkflowPerformanceAggregates($filters);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown report type: %s', $reportType));
        }

        return $reportData;
    }

    /**
     * Applique les filtres communs aux QueryBuilders.
     * @param QueryBuilder $qb Le QueryBuilder.
     * @param string $alias L'alias de l'entité principale (ex: 'c' pour Complaint).
     * @param array $filters Les filtres à appliquer.
     * @throws \DateMalformedStringException
     */
    private function applyCommonComplaintFilters(QueryBuilder $qb, string $alias, array $filters): void
    {
        $getCleanId = function (?string $idString): ?string {
            return $idString;
        };

        if (!empty($filters['roadAxisId'])) {
            $qb->andWhere(sprintf('%s.roadAxis = :roadAxisId', $alias))
                ->setParameter('roadAxisId', $getCleanId($filters['roadAxisId']));
        }

        if (!empty($filters['locationId'])) {
            $qb->andWhere(sprintf('%s.location = :locationId', $alias))
                ->setParameter('locationId', $getCleanId($filters['locationId']));
        }
        if (!empty($filters['complaintTypeId'])) {
            $qb->andWhere(sprintf('%s.complaintType = :complaintTypeId', $alias))
                ->setParameter('complaintTypeId', $getCleanId($filters['complaintTypeId']));
        }
        if (!empty($filters['startDate'])) {
            $qb->andWhere(sprintf('%s.declarationDate >= :startDate', $alias))
                ->setParameter('startDate', new \DateTimeImmutable($filters['startDate']));
        }
        if (!empty($filters['endDate'])) {
            $qb->andWhere(sprintf('%s.declarationDate <= :endDate', $alias))
                ->setParameter('endDate', (new \DateTimeImmutable($filters['endDate']))->setTime(23, 59, 59));
        }
        if (!empty($filters['statusName'])) {
            $qb->join(sprintf('%s.currentWorkflowStep', $alias), 'cws')
                ->andWhere('cws.name = :statusName')
                ->setParameter('statusName', $filters['statusName']);
        }
        if (!empty($filters['assignedToUserId'])) {
            $qb->andWhere(sprintf('%s.assignedTo = :assignedToUserId', $alias))
                ->setParameter('assignedToUserId', $getCleanId($filters['assignedToUserId']));
        }
    }

    /**
     * @throws \DateMalformedStringException
     */
    private function getComplaintSummaryData(array $filters): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('c.id, c.declarationDate, c.description')
            ->addSelect('comp.displayName AS complainantFullName') // Plaignant
            ->addSelect('ct.value AS complaintTypeName') // Type de plainte
            ->addSelect('cws.name AS currentWorkflowStepName') // Étape actuelle
            ->addSelect('ic.value AS incidentCauseName') // Cause de l'incident
            ->addSelect('loc.name AS locationName') // Localisation
            ->addSelect('ra.name AS roadAxisName') // Localisation
            ->from(Complaint::class, 'c')
            ->leftJoin('c.complainant', 'comp')
            ->leftJoin('c.complaintType', 'ct')
            ->leftJoin('c.currentWorkflowStep', 'cws')
            ->leftJoin('c.incidentCause', 'ic')
            ->leftJoin('c.location', 'loc')
            ->leftJoin('c.roadAxis', 'ra')
            ->orderBy('c.declarationDate', 'ASC');

        $this->applyCommonComplaintFilters($qb, 'c', $filters);

        return $qb->getQuery()->getResult();
    }

    private function getComplaintSummaryAggregates(array $filters): array
    {
        $totalQb = $this->em->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from(Complaint::class, 'c');
        $this->applyCommonComplaintFilters($totalQb, 'c', $filters);
        $totalComplaints = (int)$totalQb->getQuery()->getSingleScalarResult();

        $openQb = $this->em->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from(Complaint::class, 'c')
            ->join('c.currentWorkflowStep', 'ws')
            ->where('ws.isFinal = :isFinal')
            ->setParameter('isFinal', false);
        $this->applyCommonComplaintFilters($openQb, 'c', $filters);
        $openComplaints = (int)$openQb->getQuery()->getSingleScalarResult();

        $avgResolutionQb = $this->em->createQueryBuilder()
            ->select('c.declarationDate, c.closureDate')
            ->from(Complaint::class, 'c')
            ->where('c.closureDate IS NOT NULL');
        $this->applyCommonComplaintFilters($avgResolutionQb, 'c', $filters);
        $closedComplaintsData = $avgResolutionQb->getQuery()->getResult();

        $totalDays = 0;
        $count = 0;
        foreach ($closedComplaintsData as $data) {
            if ($data['declarationDate'] && $data['closureDate']) {
                $interval = $data['declarationDate']->diff($data['closureDate']);
                $totalDays += $interval->days;
                $count++;
            }
        }
        $averageResolutionTimeDays = $count > 0 ? (float)($totalDays / $count) : null;

        return [
            'totalComplaints' => $totalComplaints,
            'openComplaints' => $openComplaints,
            'averageResolutionTimeDays' => $averageResolutionTimeDays,
        ];
    }

    /*private function getPapImpactData(array $filters): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('p.id, p.name, p.contactPersonName, p.contactPersonPhone, p.contactPersonEmail')
            ->addSelect('pt.value AS personTypeName') // Type de personne (GeneralParameter)
            ->addSelect('vd.value AS vulnerabilityDegreeName') // Degré de vulnérabilité (GeneralParameter)
            ->addSelect('loc.name AS affectedLocationName') // Localisation affectée (Location)
            ->addSelect('partial papAffectationDetails.{id, affectedSurface, cu, addedValue, annexSurface, commercialActivityAffected, numberOfDaysAffected, rentalIncomeLoss, relocationAssistance, vulnerablePersonAssistance, totalDollarEquivalent, siteReleaseAgreement}')
            ->addSelect('pad_pt.value AS propertyTypeName') // Type de propriété dans PAPAffectationDetail
            ->addSelect('c.id AS complaintId') // ID de la plainte liée
            ->from(PAP::class, 'p')
            ->leftJoin('p.personType', 'pt')
            ->leftJoin('p.vulnerabilityDegree', 'vd')
            ->leftJoin('p.affectedLocation', 'loc')
            ->leftJoin('p.papAffectationDetails', 'pad')
            ->leftJoin('pad.complaint', 'c') // Plainte liée au détail d'affectation
            ->leftJoin('pad.propertyType', 'pad_pt') // Type de propriété pour le détail d'affectation
            ->orderBy('p.name', 'ASC');

        if (!empty($filters['locationId']) || !empty($filters['complaintTypeId']) || !empty($filters['startDate']) || !empty($filters['endDate'])) {
            $qb
                ->leftJoin('p.papAffectationDetails', 'pad_filter')
                ->leftJoin('pad_filter.complaint', 'c_filter');
            $this->applyCommonComplaintFilters($qb, 'c_filter', $filters);
        }

        $results = $qb->getQuery()->getResult();

        // Réorganiser les données pour que les détails d'affectation soient sous chaque PAP
        $structuredPAPs = [];
        foreach ($results as $row) {
            // Si les IDs sont de simples chaînes, on ne peut pas appeler toRfc4122() ou toString() sur elles.
            // On s'assure que l'ID est bien une chaîne.
            $papId = (string)$row['id'];
            if (!isset($structuredPAPs[$papId])) {
                $structuredPAPs[$papId] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'personTypeName' => $row['personTypeName'],
                    'contactPersonName' => $row['contactPersonName'],
                    'contactPersonPhone' => $row['contactPersonPhone'],
                    'contactPersonEmail' => $row['contactPersonEmail'],
                    'vulnerabilityDegreeName' => $row['vulnerabilityDegreeName'],
                    'affectedLocationName' => $row['affectedLocationName'],
                    'affectationDetails' => [],
                ];
            }
            if ($row['papAffectationDetails']['id'] !== null) { // S'assurer qu'il y a des détails d'affectation
                $detail = $row['papAffectationDetails'];
                $detail['propertyTypeName'] = $row['propertyTypeName']; // Ajouter le nom du type de propriété
                $detail['complaintId'] = $row['complaintId']; // Ajouter l'ID de la plainte liée
                $structuredPAPs[$papId]['affectationDetails'][] = $detail;
            }
        }
        return array_values($structuredPAPs); // Retourne un tableau simple des PAPs
    }*/

    /**
     * Récupère les agrégats pour le rapport d'impact des PAP (exemple).
     */
    /*private function getPapImpactAggregates(array $filters): array
    {
        $totalPAPsQb = $this->em->createQueryBuilder()
            ->select('COUNT(DISTINCT p.id)') // Compter les PAPs uniques
            ->from(PAP::class, 'p');

        // Si des filtres sur les plaintes sont appliqués, les appliquer ici aussi en joignant PAPAffectationDetail
        if (!empty($filters['locationId']) || !empty($filters['complaintTypeId']) || !empty($filters['startDate']) || !empty($filters['endDate'])) {
            $totalPAPsQb->join('p.papAffectationDetails', 'pad')
                ->join('pad.complaint', 'c');
            $this->applyCommonComplaintFilters($totalPAPsQb, 'c', $filters);
        }
        $totalPAPs = (int)$totalPAPsQb->getQuery()->getSingleScalarResult();

        // Calcul du total des compensations estimées
        $totalCompensationQb = $this->em->createQueryBuilder()
            ->select('SUM(pad.totalDollarEquivalent)')
            ->from(PAPAffectationDetail::class, 'pad')
            ->where('pad.totalDollarEquivalent IS NOT NULL');
        // Appliquer les filtres de plaintes si nécessaire
        if (!empty($filters['locationId']) || !empty($filters['complaintTypeId']) || !empty($filters['startDate']) || !empty($filters['endDate'])) {
            $totalCompensationQb->join('pad.complaint', 'c');
            $this->applyCommonComplaintFilters($totalCompensationQb, 'c', $filters);
        }
        $totalCompensation = (float)$totalCompensationQb->getQuery()->getSingleScalarResult();

        $papsByVulnerabilityQb = $this->em->createQueryBuilder()
            ->select('gvd.value AS degree, COUNT(DISTINCT p.id) AS count')
            ->from(PAP::class, 'p')
            ->join('p.vulnerabilityDegree', 'gvd')
            ->groupBy('gvd.value');
        // Si vous voulez filtrer les PAPs par les filtres de plainte:
        // if (!empty($filters['locationId']) || ...) {
        //     $papsByVulnerabilityQb->join('p.papAffectationDetails', 'pad_filter')
        //                           ->join('pad_filter.complaint', 'c_filter');
        //     $this->applyCommonComplaintFilters($papsByVulnerabilityQb, 'c_filter', $filters);
        // }
        $papsByVulnerability = $papsByVulnerabilityQb->getQuery()->getResult();


        return [
            'totalPAPs' => $totalPAPs,
            'totalCompensation' => $totalCompensation,
            'papsByVulnerability' => $papsByVulnerability,
        ];
    }*/

    private function getWorkflowPerformanceData(array $filters): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('ch.id, ch.actionDate, ch.comments')
            ->addSelect('IDENTITY(ch.complaint) as complaintId')
            ->addSelect('ows.name AS oldWorkflowStepName')
            ->addSelect('nws.name AS newWorkflowStepName')
            ->addSelect('wa.label AS actionName')
            ->addSelect('u.displayName AS actorName')
            ->from(ComplaintHistory::class, 'ch')
            ->leftJoin('ch.complaint', 'c')
            ->leftJoin('ch.oldWorkflowStep', 'ows')
            ->leftJoin('ch.newWorkflowStep', 'nws')
            ->leftJoin('ch.action', 'wa')
            ->leftJoin('ch.actor', 'u')
            ->orderBy('ch.actionDate', 'ASC');

        if (!empty($filters['startDate'])) {
            $qb->andWhere('ch.actionDate >= :startDate')
                ->setParameter('startDate', new \DateTimeImmutable($filters['startDate']));
        }
        if (!empty($filters['endDate'])) {
            $qb->andWhere('ch.actionDate <= :endDate')
                ->setParameter('endDate', (new \DateTimeImmutable($filters['endDate']))->setTime(23, 59, 59));
        }
        if (!empty($filters['complaintTypeId'])) {
            $qb->andWhere('IDENTITY(c.complaintType) = :complaintTypeId')
                ->setParameter('complaintTypeId', $filters['complaintTypeId']);
        }
        if (!empty($filters['locationId'])) {
            $qb->andWhere('IDENTITY(c.location) = :locationId')
                ->setParameter('locationId', $filters['locationId']);
        }
        if (!empty($filters['roadAxisId'])) {
            $qb->andWhere('IDENTITY(c.roadAxis) = :roadAxisId')
                ->setParameter('roadAxisId', $filters['roadAxisId']);
        }


        $results = $qb->getQuery()->getResult();

        $processedResults = [];
        $complaintLastActionDates = [];

        foreach ($results as $row) {
            $complaintIdString = (string)$row['complaintId'];

            $durationInDays = null;
            if (isset($complaintLastActionDates[$complaintIdString])) {
                $lastActionDate = $complaintLastActionDates[$complaintIdString]['date'];
                $interval = $lastActionDate->diff($row['actionDate']);
                $durationInDays = $interval->days;
            }

            $row['durationInDays'] = $durationInDays;
            $processedResults[] = $row;

            $complaintLastActionDates[$complaintIdString] = [
                'date' => $row['actionDate'],
                'stepName' => $row['newWorkflowStepName']
            ];
        }

        return $processedResults;
    }


    private function getWorkflowPerformanceAggregates(array $filters): array
    {
        $completedComplaintsCountQb = $this->em->createQueryBuilder()
            ->select('COUNT(DISTINCT c.id)')
            ->from(Complaint::class, 'c')
            ->join('c.currentWorkflowStep', 'ws')
            ->where('ws.isFinal = :isFinal')
            ->setParameter('isFinal', true);

        if (!empty($filters['startDate'])) {
            $completedComplaintsCountQb->andWhere('c.closureDate >= :startDate')
                ->setParameter('startDate', new \DateTimeImmutable($filters['startDate']));
        }
        if (!empty($filters['endDate'])) {
            $completedComplaintsCountQb->andWhere('c.closureDate <= :endDate')
                ->setParameter('endDate', (new \DateTimeImmutable($filters['endDate']))->setTime(23, 59, 59));
        }
        $completedComplaintsCount = (int)$completedComplaintsCountQb->getQuery()->getSingleScalarResult();

        $averageTimePerStep = [];
        $workflowSteps = $this->em->getRepository(WorkflowStep::class)->findAll(); // Récupérer toutes les étapes

        foreach ($workflowSteps as $step) {
            if ($step->isIsInitial() || $step->isIsFinal()) {
                continue;
            }

            $qbEntries = $this->em->createQueryBuilder()
                ->select('IDENTITY(ch.complaint) as complaintId, ch.actionDate') // Utilise IDENTITY()
                ->from(ComplaintHistory::class, 'ch')
                ->join('ch.newWorkflowStep', 'nws')
                ->where('IDENTITY(nws) = :stepId') // Utilise IDENTITY()
                ->setParameter('stepId', (string)$step->getId());

            $qbExits = $this->em->createQueryBuilder()
                ->select('IDENTITY(ch.complaint) as complaintId, ch.actionDate') // Utilise IDENTITY()
                ->from(ComplaintHistory::class, 'ch')
                ->join('ch.oldWorkflowStep', 'ows')
                ->where('IDENTITY(ows) = :stepId') // Utilise IDENTITY()
                ->setParameter('stepId', (string)$step->getId());

            if (!empty($filters['startDate'])) {
                $qbEntries->andWhere('ch.actionDate >= :startDate')->setParameter('startDate', new \DateTimeImmutable($filters['startDate']));
                $qbExits->andWhere('ch.actionDate >= :startDate')->setParameter('startDate', new \DateTimeImmutable($filters['startDate']));
            }
            if (!empty($filters['endDate'])) {
                $qbEntries->andWhere('ch.actionDate <= :endDate')->setParameter('endDate', (new \DateTimeImmutable($filters['endDate']))->setTime(23, 59, 59));
                $qbExits->andWhere('ch.actionDate <= :endDate')->setParameter('endDate', (new \DateTimeImmutable($filters['endDate']))->setTime(23, 59, 59));
            }

            $entries = $qbEntries->getQuery()->getResult();
            $exits = $qbExits->getQuery()->getResult();

            $entriesByComplaint = [];
            foreach ($entries as $entry) {
                $entriesByComplaint[$entry['complaintId']][] = $entry['actionDate'];
            }

            $totalStepDuration = 0;
            $stepCount = 0;

            foreach ($exits as $exit) {
                $complaintId = $exit['complaintId'];
                if (isset($entriesByComplaint[$complaintId])) {
                    foreach ($entriesByComplaint[$complaintId] as $entryDate) {
                        if ($entryDate <= $exit['actionDate']) {
                            $interval = $entryDate->diff($exit['actionDate']);
                            $totalStepDuration += $interval->days;
                            $stepCount++;
                            break;
                        }
                    }
                }
            }
            $averageTime = $stepCount > 0 ? (float)($totalStepDuration / $stepCount) : 0;
            $averageTimePerStep[] = [
                'stepName' => $step->getName(),
                'averageTime' => $averageTime,
            ];
        }

        return [
            'completedComplaintsCount' => $completedComplaintsCount,
            'averageTimePerStep' => $averageTimePerStep,
        ];
    }
}
