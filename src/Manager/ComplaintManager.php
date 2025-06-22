<?php

namespace App\Manager;


use App\Dto\Complaint\AttachedFileDto;
use App\Entity\AttachedFile;
use App\Entity\Victim;
use App\Entity\Complaint;
use App\Entity\Complainant;
use App\Entity\WorkflowStep;
use App\Entity\AffectedSpecies;
use App\Entity\WorkflowTransition;
use App\Entity\ComplaintConsequence;
use Doctrine\ORM\EntityManagerInterface;
use App\Dto\Complaint\ComplaintCreateDTO;
use App\Exception\UnavailableDataException;
use App\Message\ComplaintRegisteredMessage;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class ComplaintManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security               $security,
        private MessageBusInterface    $bus
    )
    {
    }

    public function create(ComplaintCreateDTO $data): Complaint
    {
        // Handle Complainant creation or retrieval
        $complainant = null;

        // Only handle complainant creation/retrieval if the complaint is NOT anonymous
        if (!$data->isAnonymous) {
            if ($data->newComplainant) {
                $complainantRepository = $this->em->getRepository(Complainant::class);

                // Check if a complainant with the same contactPhone or contactEmail already exists
                if ($data->newComplainant->contactPhone) {
                    $existingComplainant = $complainantRepository->findOneBy(['contactPhone' => $data->newComplainant->contactPhone]);
                    if ($existingComplainant) {
                        $complainant = $existingComplainant;
                    }
                }

                if (!$complainant && $data->newComplainant->contactEmail) {
                    $existingComplainant = $complainantRepository->findOneBy(['contactEmail' => $data->newComplainant->contactEmail]);
                    if ($existingComplainant) {
                        $complainant = $existingComplainant;
                    }
                }

                // If no existing complainant found, create a new one
                if (!$complainant) {
                    $complainant = (new Complainant())
                        ->setFirstName($data->newComplainant->firstName)
                        ->setLastName($data->newComplainant->lastName)
                        ->setMiddleName($data->newComplainant->middleName)
                        ->setContactPhone($data->newComplainant->contactPhone)
                        ->setContactEmail($data->newComplainant->contactEmail)
                        ->setPersonType($data->newComplainant->personType)
                        ->setOrganizationStatus($data->newComplainant->organizationStatus)
                        ->setLegalPersonality($data->newComplainant->legalPersonality)
                        ->setAddress($data->newComplainant->address)
                        ->setProvince($data->newComplainant->province)
                        ->setTerritory($data->newComplainant->territory)
                        ->setCommune($data->newComplainant->commune)
                        ->setQuartier($data->newComplainant->quartier)
                        ->setCity($data->newComplainant->city)
                        ->setVillage($data->newComplainant->village)
                        ->setSecteur($data->newComplainant->secteur);

                    $this->em->persist($complainant);
                }
            }
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

        if (!$data->isAnonymous) {
            $this->bus->dispatch(
                new ComplaintRegisteredMessage(
                    complaintId: $complaint->getId(),
                    complaintEmail: $complainant->getContactEmail(),
                    complaintPhone: $complainant->getContactPhone(),
                )
            );
        }
        return $complaint;
    }

    public function uploadFiles(Complaint $complaint, AttachedFileDto $dto): Complaint
    {
        $attachedFile = (new AttachedFile())
            ->setComplaint($complaint)
            ->setFiletype($dto->fileType)
            ->setWorkflowStep($dto->workflowStep);

        $attachedFile->setFile($dto->file);

        if ($dto->fileName)
            $attachedFile->setFileName($dto->fileName);

        $attachedFile->setUploadedBy($this->security->getUser());

        $this->em->persist($attachedFile);
        $this->em->flush();

        return $complaint;
    }
}
