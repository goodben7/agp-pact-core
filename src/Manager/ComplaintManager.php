<?php

namespace App\Manager;


use App\Dto\Complaint\ComplaintCreateDTO;
use App\Entity\AffectedSpecies;
use App\Entity\Complainant;
use App\Entity\Complaint;
use App\Entity\ComplaintConsequence;
use App\Entity\User;
use App\Entity\Victim;
use App\Entity\WorkflowStep;
use App\Entity\WorkflowTransition;
use App\Exception\UnavailableDataException;
use App\Message\ComplaintRegisteredMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class ComplaintManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private MessageBusInterface    $bus
    )
    {
    }

    public function create(ComplaintCreateDTO $data): Complaint
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $complainant = $data->complainant;
        if (is_null($complainant)) {
            $complainant = $this->em->getRepository(Complainant::class)->findOneBy(['userId' => $user->getId()]);
            if (!$complainant)
                throw new UnavailableDataException('No complainant found for user');
        }

        $complaint = (new Complaint())
            ->setComplaintType($data->complaintType)
            ->setIncidentDate($data->incidentDate)
            ->setIncidentCause($data->incidentCause)
            ->setDescription($data->description)
            ->setRoadAxis($data->roadAxis)
            ->setLocationDetail($data->locationDetail)
            ->setLocation($data->location)
            ->setLatitude($data->latitude)
            ->setLongitude($data->longitude)
            ->setComplainant($complainant)
            ->setAssignedTo($data->assignedTo)
            ->setDeclarationDate(new \DateTimeImmutable());

        $initialStep = $this->em->getRepository(WorkflowStep::class)->findOneBy(['isInitial' => true]);
        if (!$initialStep)
            throw new UnavailableDataException('No initial workflow step found');
        $complaint->setCurrentWorkflowStep($initialStep);

        $firstTransition = $this->em->getRepository(WorkflowTransition::class)->findOneBy(['fromStep' => $initialStep]);
        if ($firstTransition)
            $complaint->setCurrentWorkflowAction($firstTransition->getAction());
        else
            $complaint->setCurrentWorkflowAction(null);

        if ($data->victims) {
            foreach ($data->victims as $victimDto) {
                $victim = (new Victim())
                    ->setFirstName($victimDto->firstName)
                    ->setLastName($victimDto->lastName)
                    ->setMiddleName($victimDto->middleName)
                    ->setGender($victimDto->gender)
                    ->setAge($victimDto->age)
                    ->setVulnerabilityDegree($victimDto->vulnerabilityDegree)
                    ->setVictimDescription($victimDto->victimDescription)
                    ->setFamilyRelationship($victimDto->familyRelationship);

                $complaint->addVictim($victim);
            }
        }

        if ($data->affectedSpecies) {
            foreach ($data->affectedSpecies as $affectedSpeciesDto) {
                $affectedSpecie = (new AffectedSpecies())
                    ->setDescription($affectedSpeciesDto->description)
                    ->setSpeciesType($affectedSpeciesDto->speciesType)
                    ->setAffectedQuantity($affectedSpeciesDto->affectedQuantity)
                    ->setAffectedUnit($affectedSpeciesDto->affectedUnit)
                    ->setAssetType($affectedSpeciesDto->assetType);

                $complaint->addAffectedSpecies($affectedSpecie);
            }
        }

        if ($data->complaintConsequences) {
            foreach ($data->complaintConsequences as $consequenceDto) {
                $consequence = (new ComplaintConsequence())
                    ->setConsequenceType($consequenceDto->consequenceType)
                    ->setSeverity($consequenceDto->severity)
                    ->setEstimatedCost($consequenceDto->estimatedCost)
                    ->setImpactDescription($consequenceDto->impactDescription)
                    ->setAffectedQuantity($consequenceDto->affectedQuantity)
                    ->setAffectedUnit($consequenceDto->affectedUnit)
                    ->setAffectedAssetType($consequenceDto->affectedAssetType);

                $complaint->addConsequence($consequence);
            }
        }

        $this->em->persist($complaint);
        $this->em->flush();

        $this->bus->dispatch(
            new ComplaintRegisteredMessage(
                complaintId: $complaint->getId(),
                complaintEmail: $complainant->getContactEmail(),
                complaintPhone: $complainant->getContactPhone(),
            )
        );

        return $complaint;
    }
}
