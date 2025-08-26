<?php

namespace App\Repository;

use App\Entity\Complaint;
use App\Entity\Company;
use App\Entity\DefaultAssignmentRule;
use App\Entity\Location;
use App\Entity\RoadAxis;
use App\Entity\WorkflowStep;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Complaint>
 */
class ComplaintRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Complaint::class);
    }


    public function findAssignableCompanies(
        WorkflowStep $workflowStep,
        ?Location $location,
        ?RoadAxis $roadAxis,
        ?bool $isSensitive = false
    ): array {
        // Trouver toutes les règles d'assignation applicables
        $rules = $this->findApplicableRules($workflowStep, $location, $roadAxis);

        $assignableCompanies = [];

        foreach ($rules as $rule) {
            $companies = $rule->getAssignedCompanies();

            foreach ($companies as $company) {
                // Vérifier si la compagnie peut traiter les plaintes sensibles si nécessaire
                if ($isSensitive && !$company->isCanProcessSensitiveComplaint()) {
                    continue;
                }

                // Éviter les doublons
                if (!in_array($company, $assignableCompanies, true)) {
                    $assignableCompanies[] = $company;
                }
            }
        }

        // Si aucune règle spécifique, retourner toutes les compagnies actives compatibles
        if (empty($assignableCompanies)) {
            return $this->findDefaultAssignableCompanies($isSensitive, $location, $roadAxis);
        }

        return $assignableCompanies;
    }

    /**
     * Trouve les règles d'assignation applicables pour une étape de workflow donnée
     *
     * @param WorkflowStep $workflowStep
     * @param Location|null $location
     * @param RoadAxis|null $roadAxis
     * @return DefaultAssignmentRule[]
     */
    public function findApplicableRules(
        WorkflowStep $workflowStep,
        ?Location $location,
        ?RoadAxis $roadAxis
    ): array {
        $qb = $this->getEntityManager()->getRepository(DefaultAssignmentRule::class)->createQueryBuilder('r');

        $qb->where('r.workflowStep = :step')
            ->setParameter('step', $workflowStep);

        $conditions = [];

        // Si la plainte a une localisation, chercher les règles qui correspondent
        if ($location) {
            $conditions[] = 'r.location = true';
        }

        // Si la plainte a un axe routier, chercher les règles qui correspondent
        if ($roadAxis) {
            $conditions[] = 'r.roadAxis = true';
        }

        // Si aucune condition spécifique, chercher les règles générales
        if (empty($conditions)) {
            $conditions[] = '(r.location IS NULL OR r.location = false) AND (r.roadAxis IS NULL OR r.roadAxis = false)';
        }

        // Ajouter les conditions avec OR
        if (!empty($conditions)) {
            $qb->andWhere('(' . implode(' OR ', $conditions) . ')');
        }

        $qb->orderBy('r.priority', 'DESC')
            ->addOrderBy('r.id', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les compagnies assignables par défaut
     *
     * @param bool|null $isSensitive
     * @param Location|null $location
     * @param RoadAxis|null $roadAxis
     * @return Company[]
     */
    public function findDefaultAssignableCompanies(
        ?bool $isSensitive = false,
        ?Location $location = null,
        ?RoadAxis $roadAxis = null
    ): array {
        $qb = $this->getEntityManager()->getRepository(Company::class)->createQueryBuilder('c')
            ->where('c.active = true')
            ->andWhere('c.deleted = false OR c.deleted IS NULL');

        // Filtrer par capacité à traiter les plaintes sensibles
        if ($isSensitive) {
            $qb->andWhere('c.canProcessSensitiveComplaint = true');
        }

        // Filtrer par localisation si spécifiée
        if ($location) {
            $qb
                ->join('c.locations', 'cl')
                ->andWhere('cl.id = :locationId')
                ->setParameter('locationId', $location->getId());
        }

        // Filtrer par axe routier si spécifié
        if ($roadAxis) {
            $qb
                ->join('c.roadAxes', 'cr')
                ->andWhere('cr.id = :roadAxisId')
                ->setParameter('roadAxisId', $roadAxis->getId());
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve la meilleure règle d'assignation correspondante
     *
     * @param WorkflowStep $step
     * @param Location|null $location
     * @param RoadAxis|null $roadAxis
     * @return DefaultAssignmentRule|null
     */
    public function findBestMatchingRule(WorkflowStep $step, ?Location $location, ?RoadAxis $roadAxis): ?DefaultAssignmentRule
    {
        $qb = $this->getEntityManager()->getRepository(DefaultAssignmentRule::class)->createQueryBuilder('r');

        $qb->where('r.workflowStep = :step')
            ->setParameter('step', $step);

        // Construire les conditions pour la localisation et l'axe routier
        $conditions = [];

        // Si la plainte a une localisation, chercher les règles qui correspondent
        if ($location) {
            $conditions[] = 'r.location = true';
        }

        // Si la plainte a un axe routier, chercher les règles qui correspondent
        if ($roadAxis) {
            $conditions[] = 'r.roadAxis = true';
        }

        // Si aucune condition spécifique, chercher les règles générales
        if (empty($conditions)) {
            $conditions[] = '(r.location IS NULL OR r.location = false) AND (r.roadAxis IS NULL OR r.roadAxis = false)';
        }

        // Ajouter les conditions avec OR
        if (!empty($conditions)) {
            $qb->andWhere('(' . implode(' OR ', $conditions) . ')');
        }

        $qb->orderBy('r.priority', 'DESC')
            ->addOrderBy('r.id', 'ASC') // Pour avoir un ordre déterministe
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
