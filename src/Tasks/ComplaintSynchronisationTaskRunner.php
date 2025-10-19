<?php

namespace App\Tasks;

use App\Entity\Task;
use App\Model\TaskInterface;
use Psr\Log\LoggerInterface;
use App\Manager\ComplaintManager;
use App\Model\TaskRunnerInterface;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Dto\Complaint\ComplaintCreateDTO;
use App\Dto\Complainant\ComplainantCreateDTO;
use App\Dto\Victim\VictimCreateDTO;
use App\Dto\OffenderCreateDTO;
use App\Dto\Complaint\AffectedSpeciesCreateDTO;
use App\Dto\Complaint\ComplaintConsequenceCreateDTO;
use App\Exception\InvalidActionInputException;
use App\Entity\GeneralParameter;
use App\Entity\Location;
use App\Entity\RoadAxis;
use App\Entity\Prejudice;
use App\Entity\Species;

class ComplaintSynchronisationTaskRunner implements TaskRunnerInterface
{
    public const SUPPORT_TYPE = "COMPLAINT";

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface        $logger,
        private readonly ComplaintManager        $manager,
        private readonly TaskRepository $repository,
        private readonly ManagerRegistry $managerRegistry,
    )
    {
    }

    public function support(string $type): bool
    {
        return $type === self::SUPPORT_TYPE;
    }

    public function run(TaskInterface $task): void
    {
        try {
            if ($task->getMethod() === Task::METHOD_CREATE) {
                $this->logger->info('Starting to process complaint record: ' . json_encode($task->getData()));
                
                $this->create($task);
                
                $this->logger->info('Processing complaint record successfully: ' . json_encode($task->getData()));
            } else {
                $this->logger->warning("No runner found to handle task {$task->getId()} of method {$task->getMethod()}");
                throw new InvalidActionInputException("No runner found to handle task {$task->getId()} of method {$task->getMethod()}");
            }
        } catch (\Exception $e) {
            $this->managerRegistry->resetManager();
            $this->repository->updateTaskStatus($task->getId(), Task::STATUS_FAILED, $e->getMessage());
            $this->logger->info(sprintf('Complaint Synchronisation Task Runner with ID %s failed', $task->getId()));
            $this->logger->error($e->getMessage());
        }
    }

    private function create(TaskInterface $task): void
    {
        try {
            $dto = new ComplaintCreateDTO();
            
            // Map basic complaint properties
            $dto->externalReferenceId = $task->getExternalReferenceId();
            $dto->description = $task->getDataValue('description');
            $dto->locationDetail = $task->getDataValue('locationDetail');
            $dto->latitude = $task->getDataValue('latitude');
            $dto->longitude = $task->getDataValue('longitude');
            $dto->isAnonymous = $task->getDataValue('isAnonymous', false);
            
            // Handle date fields
            $incidentDate = $task->getDataValue('incidentDate');
            if ($incidentDate) {
                // Try to parse ISO format first (Y-m-d\TH:i:s.000\Z)
                $date = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.000\Z', $incidentDate);
                if (!$date) {
                    // If that fails, try simple Y-m-d format
                    $date = \DateTimeImmutable::createFromFormat('Y-m-d', $incidentDate);
                }
                $dto->incidentDate = $date ?: null;
            }
            
            // Handle entity references
            $complaintTypeId = $task->getDataValue('complaintTypeId');
            if ($complaintTypeId) {
                $dto->complaintType = $this->em->getRepository(GeneralParameter::class)->find($complaintTypeId);
            }
            
            $roadAxisId = $task->getDataValue('roadAxisId');
            if ($roadAxisId) {
                $dto->roadAxis = $this->em->getRepository(RoadAxis::class)->find($roadAxisId);
            }
            
            $locationId = $task->getDataValue('locationId');
            if ($locationId) {
                $dto->location = $this->em->getRepository(Location::class)->find($locationId);
            }
            
            // Handle incident causes (prejudices)
            $incidentCauseIds = $task->getDataValue('incidentCauseIds', []);
            if (!empty($incidentCauseIds)) {
                $dto->incidentCauses = [];
                foreach ($incidentCauseIds as $prejudiceId) {
                    $prejudice = $this->em->getRepository(Prejudice::class)->find($prejudiceId);
                    if ($prejudice) {
                        $dto->incidentCauses[] = $prejudice;
                    }
                }
            }
            
            // Handle complainant data
            $complainantData = $task->getDataValue('complainant');
            if ($complainantData && !$dto->isAnonymous) {
                $complainantDto = new ComplainantCreateDTO();
                $complainantDto->firstName = $complainantData['firstName'] ?? null;
                $complainantDto->middleName = $complainantData['middleName'] ?? null;
                $complainantDto->lastName = $complainantData['lastName'] ?? null;
                $complainantDto->contactPhone = $complainantData['contactPhone'] ?? null;
                $complainantDto->contactEmail = $complainantData['contactEmail'] ?? null;
                $complainantDto->address = $complainantData['address'] ?? null;
                
                // Handle complainant entity references
                if (isset($complainantData['personTypeId'])) {
                    $complainantDto->personType = $this->em->getRepository(GeneralParameter::class)->find($complainantData['personTypeId']);
                }
                if (isset($complainantData['organizationStatusId'])) {
                    $complainantDto->organizationStatus = $this->em->getRepository(GeneralParameter::class)->find($complainantData['organizationStatusId']);
                }
                if (isset($complainantData['legalPersonalityId'])) {
                    $complainantDto->legalPersonality = $this->em->getRepository(GeneralParameter::class)->find($complainantData['legalPersonalityId']);
                }
                if (isset($complainantData['provinceId'])) {
                    $complainantDto->province = $this->em->getRepository(Location::class)->find($complainantData['provinceId']);
                }
                if (isset($complainantData['territoryId'])) {
                    $complainantDto->territory = $this->em->getRepository(Location::class)->find($complainantData['territoryId']);
                }
                if (isset($complainantData['communeId'])) {
                    $complainantDto->commune = $this->em->getRepository(Location::class)->find($complainantData['communeId']);
                }
                if (isset($complainantData['quartierId'])) {
                    $complainantDto->quartier = $this->em->getRepository(Location::class)->find($complainantData['quartierId']);
                }
                if (isset($complainantData['cityId'])) {
                    $complainantDto->city = $this->em->getRepository(Location::class)->find($complainantData['cityId']);
                }
                if (isset($complainantData['villageId'])) {
                    $complainantDto->village = $this->em->getRepository(Location::class)->find($complainantData['villageId']);
                }
                if (isset($complainantData['secteurId'])) {
                    $complainantDto->secteur = $this->em->getRepository(Location::class)->find($complainantData['secteurId']);
                }
                
                $dto->newComplainant = $complainantDto;
            }
            
            // Handle victims
            $victimsData = $task->getDataValue('victims', []);
            if (!empty($victimsData)) {
                $dto->victims = [];
                foreach ($victimsData as $victimData) {
                    $victimDto = new VictimCreateDTO();
                    $victimDto->firstName = $victimData['firstName'] ?? null;
                    $victimDto->middleName = $victimData['middleName'] ?? null;
                    $victimDto->lastName = $victimData['lastName'] ?? null;
                    $victimDto->age = $victimData['age'] ?? null;
                    $victimDto->victimDescription = $victimData['victimDescription'] ?? null;
    
    
                    if (isset($victimData['familyRelationship'])) {
                        $victimDto->gender = $this->em->getRepository(GeneralParameter::class)->find($victimData['familyRelationship']);
                    }
                    if (isset($victimData['relationshipProject'])) {
                        $victimDto->gender = $this->em->getRepository(GeneralParameter::class)->find($victimData['relationshipProject']);
                    }
                    if (isset($victimData['genderId'])) {
                        $victimDto->gender = $this->em->getRepository(GeneralParameter::class)->find($victimData['genderId']);
                    }
                    if (isset($victimData['vulnerabilityDegreeId'])) {
                        $victimDto->vulnerabilityDegree = $this->em->getRepository(GeneralParameter::class)->find($victimData['vulnerabilityDegreeId']);
                    }
                    
                    $dto->victims[] = $victimDto;
                }
            }
            
            // Handle offenders
            $offendersData = $task->getDataValue('offenders', []);
            if (!empty($offendersData)) {
                $dto->offenders = [];
                foreach ($offendersData as $offenderData) {
                    $offenderDto = new OffenderCreateDTO();
                    $offenderDto->firstName = $offenderData['firstName'] ?? null;
                    $offenderDto->middleName = $offenderData['middleName'] ?? null;
                    $offenderDto->lastName = $offenderData['lastName'] ?? null;
                    $offenderDto->age = $offenderData['age'] ?? null;
                    $offenderDto->description = $offenderData['description'] ?? null;
                    
                    if (isset($offenderData['relationshipProject'])) {
                        $offenderDto->gender = $this->em->getRepository(GeneralParameter::class)->find($offenderData['relationshipProject']);
                    }
                    if (isset($offenderData['genderId'])) {
                        $offenderDto->gender = $this->em->getRepository(GeneralParameter::class)->find($offenderData['genderId']);
                    }
                    
                    $dto->offenders[] = $offenderDto;
                }
            }
            
            // Handle affected species
            $affectedSpeciesData = $task->getDataValue('affectedSpecies', []);
            if (!empty($affectedSpeciesData)) {
                $dto->affectedSpecies = [];
                foreach ($affectedSpeciesData as $speciesData) {
                    $speciesDto = new AffectedSpeciesCreateDTO();
                    $speciesDto->description = $speciesData['description'] ?? null;
                    $speciesDto->affectedQuantity = $speciesData['affectedQuantity'] ?? null;
                    
                    if (isset($speciesData['speciesTypeId'])) {
                        $speciesDto->speciesType = $this->em->getRepository(Species::class)->find($speciesData['speciesTypeId']);
                    }
                    if (isset($speciesData['affectedUnitId'])) {
                        $speciesDto->affectedUnit = $this->em->getRepository(GeneralParameter::class)->find($speciesData['affectedUnitId']);
                    }
                    if (isset($speciesData['assetTypeId'])) {
                        $speciesDto->assetType = $this->em->getRepository(GeneralParameter::class)->find($speciesData['assetTypeId']);
                    }
                    
                    $dto->affectedSpecies[] = $speciesDto;
                }
            }
            
            // Handle complaint consequences
            $consequencesData = $task->getDataValue('consequences', []);
            if (!empty($consequencesData)) {
                $dto->complaintConsequences = [];
                foreach ($consequencesData as $consequenceData) {
                    $consequenceDto = new ComplaintConsequenceCreateDTO();
                    $consequenceDto->estimatedCost = $consequenceData['estimatedCost'] ?? null;
                    $consequenceDto->impactDescription = $consequenceData['impactDescription'] ?? null;
                    $consequenceDto->affectedQuantity = $consequenceData['affectedQuantity'] ?? null;
                    
                    if (isset($consequenceData['consequenceTypeId'])) {
                        $consequenceDto->consequenceType = $this->em->getRepository(GeneralParameter::class)->find($consequenceData['consequenceTypeId']);
                    }
                    if (isset($consequenceData['severityId'])) {
                        $consequenceDto->severity = $this->em->getRepository(GeneralParameter::class)->find($consequenceData['severityId']);
                    }
                    if (isset($consequenceData['affectedUnitId'])) {
                        $consequenceDto->affectedUnit = $this->em->getRepository(GeneralParameter::class)->find($consequenceData['affectedUnitId']);
                    }
                    if (isset($consequenceData['affectedAssetTypeId'])) {
                        $consequenceDto->affectedAssetType = $this->em->getRepository(GeneralParameter::class)->find($consequenceData['affectedAssetTypeId']);
                    }
                    
                    $dto->complaintConsequences[] = $consequenceDto;
                }
            }
            
            // Create the complaint
            $this->manager->create($dto);
            
            // Update task status
            $this->repository->updateTaskStatus($task->getId(), Task::STATUS_TERMINATED, null);
            
        } catch (\Exception $e) {
            $this->managerRegistry->resetManager();
            $this->repository->updateTaskStatus($task->getId(), Task::STATUS_FAILED, $e->getMessage());
            $this->logger->error('Error processing complaint record: ' . json_encode($task->getData()) . ' - ' . $e->getMessage());
            throw $e;
        }
    }
}