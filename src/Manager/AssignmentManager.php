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
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function assignDefaultActor(Complaint $complaint): void
    {
        $step = $complaint->getCurrentWorkflowStep();
        $location = $complaint->getLocation();
        $roadAxis = $complaint->getRoadAxis();

        $rule = $this->findBestMatchingRule($step, $location, $roadAxis);

        if (!$rule) {
            return;
        }

        if ($company = $rule->getAssignedCompany()) {
            $complaint->setInvolvedCompany($company);
        }

        if ($profile = $rule->getAssignedProfile()) {
            // Trouver un utilisateur avec ce profil et la bonne localisation/axe
            // C'est la partie la plus complexe : comment choisir UN utilisateur parmi plusieurs ?
            // Pour commencer, on peut se contenter d'assigner l'entreprise.
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

        $locationConditions = [];
        $currentLocation = $location;
        while ($currentLocation) {
            $locationConditions[] = 'r.location = :loc' . $currentLocation->getId();
            $qb->setParameter('loc' . $currentLocation->getId(), $currentLocation);
            $currentLocation = $currentLocation->getParent();
        }

        $orX = $qb->expr()->orX();
        if ($roadAxis) {
            $orX->add('r.roadAxis = :roadAxis');
            $qb->setParameter('roadAxis', $roadAxis);
        }
        if (!empty($locationConditions)) {
            foreach($locationConditions as $condition) {
                $orX->add($condition);
            }
        }
        $orX->add($qb->expr()->andX('r.location IS NULL', 'r.roadAxis IS NULL'));

        $qb->andWhere($orX)
            ->orderBy('r.priority', 'DESC')
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
