<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Complaint;
use App\Entity\Location;
use App\Entity\RoadAxis;
use App\Entity\WorkflowStep;
use App\Repository\ComplaintRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class AssignableCompaniesProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private ComplaintRepository $complaintRepository
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $complaintId = $uriVariables['complaintId'] ?? null;


        if (!$complaintId) {
            throw new NotFoundHttpException('Complaint ID is required');
        }

        $complaint = $this->em->getRepository(Complaint::class)->find($complaintId);

        if (!$complaint) {
            throw new NotFoundHttpException('Complaint not found');
        }

        // Récupérer les paramètres de filtrage depuis la requête
        $request = $context['request'] ?? null;
        $workflowStepId = $request?->query->get('workflowStepId');
        $locationId = $request?->query->get('location');
        $roadAxisId = $request?->query->get('roadAxis');

        // Utiliser les données de la plainte ou les paramètres fournis
        $workflowStep = $workflowStepId
            ? $this->em->getRepository(WorkflowStep::class)->find($workflowStepId)
            : $complaint->getCurrentWorkflowStep();

        $location = $locationId
            ? $this->em->getRepository(Location::class)->find($locationId)
            : $complaint->getLocation();

        $roadAxis = $roadAxisId
            ? $this->em->getRepository(RoadAxis::class)->find($roadAxisId)
            : $complaint->getRoadAxis();

        if (!$workflowStep) {
            return [];
        }

        return $this->complaintRepository->findAssignableCompanies($workflowStep, $location, $roadAxis, $complaint->getIsSensitive());
    }
}
