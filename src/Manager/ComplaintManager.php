<?php

namespace App\Manager;


use App\Entity\Company;
use App\Entity\Victim;
use App\Entity\Complaint;
use App\Entity\Prejudice;
use App\Entity\Complainant;
use App\Entity\AttachedFile;
use App\Entity\WorkflowStep;
use App\Entity\AffectedSpecies;
use App\Entity\WorkflowTransition;
use App\Entity\ComplaintConsequence;
use App\Dto\Complaint\AttachedFileDto;
use App\Message\AssignedMessage;
use Doctrine\ORM\EntityManagerInterface;
use App\Dto\Complaint\ComplaintCreateDTO;
use App\Exception\UnavailableDataException;
use App\Message\ComplaintRegisteredMessage;
use Symfony\Bundle\SecurityBundle\Security;
use App\Constant\GeneralParameterComplaintType;
use App\Entity\User;
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
        $complainant = null;

        if (!$data->isAnonymous) {
            if ($data->newComplainant) {
                $complainantRepository = $this->em->getRepository(Complainant::class);

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

        $isSensitive = false;
        if (!empty($data->incidentCauses)) {
            $prejudiceRepository = $this->em->getRepository(Prejudice::class);

            $matchingPrejudices = $prejudiceRepository->findBy(['incidentCause' => $data->incidentCauses]);

            foreach ($matchingPrejudices as $prejudice) {
                $complaintType = $prejudice->getComplaintType();
                if ($complaintType && $complaintType->getCode() === GeneralParameterComplaintType::SENSITIVE_COMPLAINT_CODE) {
                    $isSensitive = true;
                    break;
                }
            }
        }

        if (!$isSensitive && $data->complaintConsequences) {
            $prejudiceRepository = $this->em->getRepository(Prejudice::class);

            $sensitivePrejudices = $prejudiceRepository->findByComplaintTypeCode(GeneralParameterComplaintType::SENSITIVE_COMPLAINT_CODE);

            if ($sensitivePrejudices) {
                $sensitiveConsequenceTypeIds = [];

                foreach ($sensitivePrejudices as $prejudice) {
                    foreach ($prejudice->getConsequences() as $prejudiceConsequence) {
                        $type = $prejudiceConsequence->getConsequenceType();
                        if ($type) {
                            $sensitiveConsequenceTypeIds[$type->getId()] = true;
                        }
                    }
                }

                foreach ($data->complaintConsequences as $consequenceDto) {
                    $type = $consequenceDto->consequenceType;
                    if ($type && isset($sensitiveConsequenceTypeIds[$type->getId()])) {
                        $isSensitive = true;
                        break;
                    }
                }
            }
        }
        $userId = null;

        if (!$data->isAnonymous) {

            /**
             * @var User $user
             */
            $user = $this->security->getUser();

            $userId = $user->getId();
        }

        $complaint = (new Complaint())
            ->setComplaintType($data->complaintType)
            ->setIncidentDate($data->incidentDate)
            ->setDescription($data->description)
            ->setRoadAxis($data->roadAxis)
            ->setLocationDetail($data->locationDetail)
            ->setLocation($data->location)
            ->setLatitude($data->latitude)
            ->setLongitude($data->longitude)
            ->setComplainant($complainant)
            ->setAssignedTo($data->assignedTo)
            ->setIsSensitive($isSensitive)
            ->setDeclarationDate(new \DateTimeImmutable())
            ->setCreatedBy($userId);

        if (!empty($data->incidentCauses)) {
            foreach ($data->incidentCauses as $incidentCause) {
                $complaint->addIncidentCause($incidentCause);
            }
        }


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

        $company = $this->em->getRepository(Company::class)->findOneBy(['location' => $complaint->getLocation()]);
        if ($company) {
            $this->bus->dispatch(
                new AssignedMessage(
                    complaintId: $complaint->getId(),
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
