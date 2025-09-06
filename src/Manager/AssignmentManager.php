<?php

namespace App\Manager;

use App\Entity\Complaint;
use App\Entity\Location;
use App\Entity\RoadAxis;
use App\Entity\WorkflowStep;
use App\Entity\Company;
use App\Provider\AssignableCompaniesProvider;
use App\Repository\ComplaintRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class AssignmentManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private AssignableCompaniesProvider $assignableCompaniesProvider,
        private ComplaintRepository $complaintRepository
    ) 
    {
    }

    public function assignDefaultActor(Complaint $complaint): void
    {
        $step = $complaint->getCurrentWorkflowStep();
        $location = $complaint->getLocation();
        $roadAxis = $complaint->getRoadAxis();

        $rule = $this->complaintRepository->findBestMatchingRule($step, $location, $roadAxis);

        if (!$rule) {
            return;
        }

        // Assigner une compagnie basée sur les profils (GeneralParameter) assignés
        $assignedProfiles = $rule->getAssignedProfiles();
        if (!$assignedProfiles->isEmpty()) {
            $companyType = $assignedProfiles->first(); // GeneralParameter représentant le type de compagnie
            
            // Trouver une compagnie ayant ce type
            $company = $this->findCompanyByType($companyType, $complaint->getIsSensitive(), $location, $roadAxis);
            
            if ($company) {
                $complaint->setInvolvedCompany($company);
            }
        }
    }

    /**
     * Trouve une compagnie par type (GeneralParameter) en tenant compte des critères
     */
    private function findCompanyByType(
        \App\Entity\GeneralParameter $companyType, 
        bool $isSensitive = false, 
        ?Location $location = null, 
        ?RoadAxis $roadAxis = null
    ): ?Company {
        $companyRepository = $this->em->getRepository(Company::class);
        
        $queryBuilder = $companyRepository->createQueryBuilder('c')
            ->where('c.type = :type')
            ->andWhere('c.active = true')
            ->andWhere('c.deleted = false OR c.deleted IS NULL')
            ->setParameter('type', $companyType);

        // Filtrer par capacité à traiter les plaintes sensibles
        if ($isSensitive) {
            $queryBuilder->andWhere('c.canProcessSensitiveComplaint = true');
        }

        // Filtrer par localisation si spécifiée
        if ($location) {
            $queryBuilder->join('c.locations', 'l')
                ->andWhere('l = :location')
                ->setParameter('location', $location);
        }

        // Filtrer par axe routier si spécifié
        if ($roadAxis) {
            $queryBuilder->join('c.roadAxes', 'r')
                ->andWhere('r = :roadAxis')
                ->setParameter('roadAxis', $roadAxis);
        }

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * Récupérer toutes les compagnies assignables pour une plainte
     */
    public function getAssignableCompanies(Complaint $complaint): array
    {
        return $this->complaintRepository->findAssignableCompanies(
            $complaint->getCurrentWorkflowStep(),
            $complaint->getIsSensitive(),
            $complaint->getLocation(),
            $complaint->getRoadAxis()
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
