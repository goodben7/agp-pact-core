<?php

namespace App\Manager;

use App\Entity\Complaint;
use App\Entity\DefaultAssignmentRule;
use App\Entity\Location;
use App\Entity\RoadAxis;
use App\Entity\WorkflowStep;
use App\Provider\AssignableCompaniesProvider;
use App\Repository\ComplaintRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class AssignmentManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private AssignableCompaniesProvider $assignableCompaniesProvider,
        private ComplaintRepository $complaintRepository
    ) {}

    public function assignDefaultActor(Complaint $complaint): void
    {
        $step = $complaint->getCurrentWorkflowStep();
        $location = $complaint->getLocation();
        $roadAxis = $complaint->getRoadAxis();

        $rule = $this->complaintRepository->findBestMatchingRule($step, $location, $roadAxis);

        if (!$rule) {
            return;
        }

        // Assigner la première compagnie de la collection si elle existe
        $assignedCompanies = $rule->getAssignedCompanies();
        if (!$assignedCompanies->isEmpty()) {
            $company = $assignedCompanies->first();

            // Vérifier si la compagnie peut traiter les plaintes sensibles si nécessaire
            if ($complaint->getIsSensitive() && !$company->isCanProcessSensitiveComplaint()) {
                // Chercher une autre compagnie dans la règle
                foreach ($assignedCompanies as $alternativeCompany) {
                    if ($alternativeCompany->isCanProcessSensitiveComplaint()) {
                        $company = $alternativeCompany;
                        break;
                    }
                }
            }

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

    /**
     * Récupérer toutes les compagnies assignables pour une plainte
     */
    public function getAssignableCompanies(Complaint $complaint): array
    {
        return $this->complaintRepository->findAssignableCompanies(
            $complaint->getCurrentWorkflowStep(),
            $complaint->getLocation(),
            $complaint->getRoadAxis(),
            $complaint->getIsSensitive()
        );
    }

    /**
     * Méthode pour trouver toutes les règles applicables (utile pour le debug)
     */
    public function findApplicableRules(WorkflowStep $step, ?Location $location, ?RoadAxis $roadAxis): array
    {
        return $this->complaintRepository->findApplicableRules($step, $location, $roadAxis);
    }
}
