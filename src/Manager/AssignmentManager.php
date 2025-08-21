<?php

namespace App\Manager;

use App\Entity\Complaint;
use App\Entity\DefaultAssignmentRule;
use App\Entity\Location;
use App\Entity\RoadAxis;
use App\Entity\WorkflowStep;
use Doctrine\ORM\EntityManagerInterface;

readonly class AssignmentManager
{
    public function __construct(private EntityManagerInterface $em) {}

    public function assignDefaultActor(Complaint $complaint): void
    {
        $step = $complaint->getCurrentWorkflowStep();
        $location = $complaint->getLocation();
        $roadAxis = $complaint->getRoadAxis();

        $rule = $this->findBestMatchingRule($step, $location, $roadAxis);

        if (!$rule) {
            return;
        }

        // Assigner la première compagnie de la collection si elle existe
        $assignedCompanies = $rule->getAssignedCompanies();
        if (!$assignedCompanies->isEmpty()) {
            $company = $assignedCompanies->first();
            $complaint->setInvolvedCompany($company);
        }

        // Assigner le premier profil de la collection si il existe
        $assignedProfiles = $rule->getAssignedProfiles();
        if (!$assignedProfiles->isEmpty()) {
            $profile = $assignedProfiles->first();
            // Trouver un utilisateur avec ce profil et la bonne localisation/axe
            // C'est la partie la plus complexe : comment choisir UN utilisateur parmi plusieurs ?
            // Pour l'instant, on peut se contenter d'assigner l'entreprise.
            // $user = $this->findUserByProfileAndLocation($profile, $location, $roadAxis);
            // if ($user) {
            //     $complaint->setCurrentAssignee($user);
            // }
        }
    }

    private function findBestMatchingRule(WorkflowStep $step, ?Location $location, ?RoadAxis $roadAxis): ?DefaultAssignmentRule
    {
        $qb = $this->em->getRepository(DefaultAssignmentRule::class)->createQueryBuilder('r');

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

    /**
     * Méthode pour trouver toutes les règles applicables (utile pour le debug)
     */
    public function findApplicableRules(WorkflowStep $step, ?Location $location, ?RoadAxis $roadAxis): array
    {
        $qb = $this->em->getRepository(DefaultAssignmentRule::class)->createQueryBuilder('r');

        $qb->where('r.workflowStep = :step')
            ->setParameter('step', $step);

        $conditions = [];

        if ($location) {
            $conditions[] = 'r.location = true';
        }

        if ($roadAxis) {
            $conditions[] = 'r.roadAxis = true';
        }

        if (empty($conditions)) {
            $conditions[] = '(r.location IS NULL OR r.location = false) AND (r.roadAxis IS NULL OR r.roadAxis = false)';
        }

        if (!empty($conditions)) {
            $qb->andWhere('(' . implode(' OR ', $conditions) . ')');
        }

        $qb->orderBy('r.priority', 'DESC')
            ->addOrderBy('r.id', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
