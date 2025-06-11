<?php

namespace App\Manager;


use App\Dto\Complaint\ComplaintCreateDTO;
use App\Entity\Complaint;
use App\Entity\WorkflowStep;
use App\Entity\WorkflowTransition;
use App\Exception\UnavailableDataException;
use App\Message\ComplaintRegisteredMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class ComplaintManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface    $bus
    )
    {
    }

    public function create(ComplaintCreateDTO $data): Complaint
    {
        $complaint = (new Complaint())
            ->setComplaintType($data->complaintType)
            ->setIncidentDate(new \DateTimeImmutable($data->incidentDate))
            ->setIncidentCause($data->incidentCause)
            ->setDescription($data->description)
            ->setRoadAxis($data->roadAxis)
            ->setLocationDetail($data->locationDetail)
            ->setLocation($data->location)
            ->setLatitude($data->latitude)
            ->setLongitude($data->longitude)
            ->setComplainant($data->complainant)
            ->setAssignedTo($data->assignedTo);

        $initialStep = $this->em->getRepository(WorkflowStep::class)->findOneBy(['isInitial' => true]);
        if (!$initialStep)
            throw new UnavailableDataException('No initial workflow step found');
        $complaint->setCurrentWorkflowStep($initialStep);

        $firstTransition = $this->em->getRepository(WorkflowTransition::class)->findOneBy(['fromStep' => $initialStep]);
        if ($firstTransition)
            $complaint->setCurrentWorkflowAction($firstTransition->getAction());
        else
            $complaint->setCurrentWorkflowAction(null);

        $this->em->persist($complaint);
        $this->em->flush();

        $this->bus->dispatch(
            new ComplaintRegisteredMessage(
                complaintId: $complaint->getId(),
                complaintEmail: $data->complainant->getContactEmail(),
                complaintPhone: $data->complainant->getContactPhone(),
            )
        );

        return $complaint;
    }
}
