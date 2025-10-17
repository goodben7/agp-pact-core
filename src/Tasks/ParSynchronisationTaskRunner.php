<?php

namespace App\Tasks;

use App\Entity\Par;
use App\Entity\Task;
use App\Model\NewTombsModel;
use App\Model\NewOwnerModel;
use App\Model\NewTenantModel;
use App\Model\TaskInterface;
use Psr\Log\LoggerInterface;
use App\Manager\ParManager;
use App\Model\TaskRunnerInterface;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Exception\UnavailableDataException;
use App\Exception\InvalidActionInputException;

class ParSynchronisationTaskRunner  implements TaskRunnerInterface
{
    public const SUPPORT_TYPE = "PAR";

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface        $logger,
        private readonly ParManager        $manager,
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
        try{

            if($task->getMethod() === Task::METHOD_CREATE){
                $this->logger->info('Starting to process record: ' . json_encode($task->getData()));
    
                $this->create($task);
    
                $this->logger->info('Processing record successfully: ' . json_encode($task->getData()));
            }
            elseif($task->getMethod() === Task::METHOD_UPDATE){
                $this->logger->info('Starting to process record: ' . json_encode($task->getData()));
    
                $this->update($task);
    
                $this->logger->info('Processing record successfully: ' . json_encode($task->getData()));
            }
            else{
                $this->logger->warning("no runner found to handle task {$task->getId()} of method {$task->getMethod()}");
                throw new InvalidActionInputException("no runner found to handle task {$task->getId()} of method {$task->getMethod()}");
            }

        }catch(\Exception $e){
            $this->managerRegistry->resetManager();
            $this->repository->updateTaskStatus($task->getId(), Task::STATUS_FAILED, $e->getMessage());
            $this->logger->info(sprintf('Agent Synchronisation Task Runner with ID %s failed', $task->getId()));
            $this->logger->error($e->getMessage());
        }

        
    } 

    private function create(TaskInterface $task): void
    {
        try {
            $parType = $task->getDataValue('type');
            
            if ($parType === Par::TYPE_TOMBS) {
                $this->createTombs($task);
            } elseif ($parType === Par::TYPE_OWNER) {
                $this->createOwner($task);
            } elseif ($parType === Par::TYPE_TENANT) {
                $this->createTenant($task);
            } else {
                throw new InvalidActionInputException("Unknown PAR type: {$parType}");
            }
            
            $this->repository->updateTaskStatus($task->getId(), Task::STATUS_TERMINATED, null);
            
        } catch (\Exception $e) {
            $this->managerRegistry->resetManager();
            $this->repository->updateTaskStatus($task->getId(), Task::STATUS_FAILED, $e->getMessage());
            $this->logger->error('Error processing record: ' . json_encode($task->getData()) . ' - ' . $e->getMessage());
        }
    }
    
    private function createTombs(TaskInterface $task): void
    {
        $model = new NewTombsModel();
        $model->code = $task->getDataValue('code');
        $model->declarantName = $task->getDataValue('declarantName');
        $model->declarantSexe = $task->getDataValue('declarantSexe');
        $model->declarantAge = $task->getDataValue('declarantAge');
        $model->declarantPhone = $task->getDataValue('declarantPhone');
        $model->village = $task->getDataValue('village');
        $model->deceasedNameOrDescriptionVault = $task->getDataValue('deceasedNameOrDescriptionVault');
        $model->placeOfBirthDeceased = $task->getDataValue('placeOfBirthDeceased');
        $model->externalReferenceId = $task->getExternalReferenceId();
        $dateOfBirthDeceased = $task->getDataValue('dateOfBirthDeceased');
        if ($dateOfBirthDeceased) {
            // Try to parse ISO format first (Y-m-d\TH:i:s.000\Z)
            $date = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.000\Z', $dateOfBirthDeceased);
            if (!$date) {
                // If that fails, try simple Y-m-d format
                $date = \DateTimeImmutable::createFromFormat('Y-m-d', $dateOfBirthDeceased);
            }
            $model->dateOfBirthDeceased = $date ?: null;
        } else {
            $model->dateOfBirthDeceased = null;
        }
        $model->deceasedResidence = $task->getDataValue('deceasedResidence');
        $model->spouseName = $task->getDataValue('spouseName');
        $model->measures = $task->getDataValue('measures');
        $model->totalGeneral = $task->getDataValue('totalGeneral');
        $model->isPaid = $task->getDataValue('isPaid');
        $model->remainingAmount = $task->getDataValue('remainingAmount');
        $bankAccountCreationDate = $task->getDataValue('bankAccountCreationDate');
        if ($bankAccountCreationDate) {
            // Try to parse ISO format first (Y-m-d\TH:i:s.000\Z)
            $date = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.000\Z', $bankAccountCreationDate);
            if (!$date) {
                // If that fails, try simple Y-m-d format
                $date = \DateTimeImmutable::createFromFormat('Y-m-d', $bankAccountCreationDate);
            }
            $model->bankAccountCreationDate = $date ?: null;
        } else {
            $model->bankAccountCreationDate = null;
        }
        $model->bankAccount = $task->getDataValue('bankAccount');
        $paymentDate = $task->getDataValue('paymentDate');
        if ($paymentDate) {
            // Try to parse ISO format first (Y-m-d\TH:i:s.000\Z)
            $date = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.000\Z', $paymentDate);
            if (!$date) {
                // If that fails, try simple Y-m-d format
                $date = \DateTimeImmutable::createFromFormat('Y-m-d', $paymentDate);
            }
            $model->paymentDate = $date ?: null;
        } else {
            $model->paymentDate = null;
        }
        $model->roadAxis = $task->getDataValue('roadAxis');
        
        $this->manager->CreateTombs($model);
    }
    
    private function createOwner(TaskInterface $task): void
    {
        $model = new NewOwnerModel();
        $model->code = $task->getDataValue('code');
        $model->fullname = $task->getDataValue('fullname');
        $model->sexe = $task->getDataValue('sexe');
        $model->age = $task->getDataValue('age');
        $model->phone = $task->getDataValue('phone');
        $model->identificationNumber = $task->getDataValue('identificationNumber');
        $model->formerPap = $task->getDataValue('formerPap');
        $model->kilometerPoint = $task->getDataValue('kilometerPoint');
        $model->typeLiability = $task->getDataValue('typeLiability');
        $model->province = $task->getDataValue('province');
        $model->territory = $task->getDataValue('territory');
        $model->village = $task->getDataValue('village');
        $model->externalReferenceId = $task->getExternalReferenceId();
        $model->longitude = $task->getDataValue('longitude');
        $model->latitude = $task->getDataValue('latitude');
        $model->referenceCoordinates = $task->getDataValue('referenceCoordinates');
        $model->orientation = $task->getDataValue('orientation');
        $model->vulnerability = $task->getDataValue('vulnerability');
        $model->vulnerabilityType = $task->getDataValue('vulnerabilityType');
        $model->length = $task->getDataValue('length');
        $model->wide = $task->getDataValue('wide');
        $model->areaAllocatedSquareMeters = $task->getDataValue('areaAllocatedSquareMeters');
        $model->cuPerSquareMeter = $task->getDataValue('cuPerSquareMeter');
        $model->capitalGain = $task->getDataValue('capitalGain');
        $model->totalPropertyUsd = $task->getDataValue('totalPropertyUsd');
        $model->totalBatisUsd = $task->getDataValue('totalBatisUsd');
        $model->commercialActivity = $task->getDataValue('commercialActivity');
        $model->numberWorkingDaysPerWeek = $task->getDataValue('numberWorkingDaysPerWeek');
        $model->averageDailyIncome = $task->getDataValue('averageDailyIncome');
        $model->monthlyIncome = $task->getDataValue('monthlyIncome');
        $model->totalCompensationThreeMonths = $task->getDataValue('totalCompensationThreeMonths');
        $model->affectedCultivatedArea = $task->getDataValue('affectedCultivatedArea');
        $model->equivalentUsd = $task->getDataValue('equivalentUsd');
        $model->tree = $task->getDataValue('tree');
        $model->totalFarmIncome = $task->getDataValue('totalFarmIncome');
        $model->lossRentalIncome = $task->getDataValue('lossRentalIncome');
        $model->movingAssistance = $task->getDataValue('movingAssistance');
        $model->assistanceVulnerablePersons = $task->getDataValue('assistanceVulnerablePersons');
        $model->noticeAgreementVacatingPremises = $task->getDataValue('noticeAgreementVacatingPremises');
        $model->totalGeneral = $task->getDataValue('totalGeneral');
        $model->isPaid = $task->getDataValue('isPaid');
        $model->remainingAmount = $task->getDataValue('remainingAmount');
        $bankAccountCreationDate = $task->getDataValue('bankAccountCreationDate');
        if ($bankAccountCreationDate) {
            // Try to parse ISO format first (Y-m-d\TH:i:s.000\Z)
            $date = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.000\Z', $bankAccountCreationDate);
            if (!$date) {
                // If that fails, try simple Y-m-d format
                $date = \DateTimeImmutable::createFromFormat('Y-m-d', $bankAccountCreationDate);
            }
            $model->bankAccountCreationDate = $date ?: null;
        } else {
            $model->bankAccountCreationDate = null;
        }
        $model->bankAccount = $task->getDataValue('bankAccount');
        $paymentDate = $task->getDataValue('paymentDate');
        if ($paymentDate) {
            // Try to parse ISO format first (Y-m-d\TH:i:s.000\Z)
            $date = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.000\Z', $paymentDate);
            if (!$date) {
                // If that fails, try simple Y-m-d format
                $date = \DateTimeImmutable::createFromFormat('Y-m-d', $paymentDate);
            }
            $model->paymentDate = $date ?: null;
        } else {
            $model->paymentDate = null;
        }
        $model->roadAxis = $task->getDataValue('roadAxis');
        
        $this->manager->CreateOwner($model);
    }
    
    private function createTenant(TaskInterface $task): void
    {
        $model = new NewTenantModel();
        $model->code = $task->getDataValue('code');
        $model->fullname = $task->getDataValue('fullname');
        $model->sexe = $task->getDataValue('sexe');
        $model->age = $task->getDataValue('age');
        $model->phone = $task->getDataValue('phone');
        $model->identificationNumber = $task->getDataValue('identificationNumber');
        $model->formerPap = $task->getDataValue('formerPap');
        $model->province = $task->getDataValue('province');
        $model->territory = $task->getDataValue('territory');
        $model->village = $task->getDataValue('village');
        $model->externalReferenceId = $task->getExternalReferenceId();
        $model->longitude = $task->getDataValue('longitude');
        $model->latitude = $task->getDataValue('latitude');
        $model->orientation = $task->getDataValue('orientation');
        $model->vulnerability = $task->getDataValue('vulnerability');
        $model->vulnerabilityType = $task->getDataValue('vulnerabilityType');
        $model->length = $task->getDataValue('length');
        $model->wide = $task->getDataValue('wide');
        $model->areaAllocatedSquareMeters = $task->getDataValue('areaAllocatedSquareMeters');
        $model->cuPerSquareMeter = $task->getDataValue('cuPerSquareMeter');
        $model->capitalGain = $task->getDataValue('capitalGain');
        $model->totalPropertyUsd = $task->getDataValue('totalPropertyUsd');
        $model->totalBatisUsd = $task->getDataValue('totalBatisUsd');
        $model->commercialActivity = $task->getDataValue('commercialActivity');
        $model->numberWorkingDaysPerWeek = $task->getDataValue('numberWorkingDaysPerWeek');
        $model->averageDailyIncome = $task->getDataValue('averageDailyIncome');
        $model->monthlyIncome = $task->getDataValue('monthlyIncome');
        $model->totalCompensationThreeMonths = $task->getDataValue('totalCompensationThreeMonths');
        $model->affectedCultivatedArea = $task->getDataValue('affectedCultivatedArea');
        $model->equivalentUsd = $task->getDataValue('equivalentUsd');
        $model->tree = $task->getDataValue('tree');
        $model->totalFarmIncome = $task->getDataValue('totalFarmIncome');
        $model->lossRentalIncome = $task->getDataValue('lossRentalIncome');
        $model->movingAssistance = $task->getDataValue('movingAssistance');
        $model->assistanceVulnerablePersons = $task->getDataValue('assistanceVulnerablePersons');
        $model->rentalGuaranteeAssistance = $task->getDataValue('rentalGuaranteeAssistance');
        $model->noticeAgreementVacatingPremises = $task->getDataValue('noticeAgreementVacatingPremises');
        $model->tenantMonthlyRent = $task->getDataValue('tenantMonthlyRent');
        $model->lessorName = $task->getDataValue('lessorName');
        $model->totalRent = $task->getDataValue('totalRent');
        $model->totalLossEmploymentIncome = $task->getDataValue('totalLossEmploymentIncome');
        $model->totalLossBusinessIncome = $task->getDataValue('totalLossBusinessIncome');
        $model->kilometerPoint = $task->getDataValue('kilometerPoint');
        $model->category = $task->getDataValue('category');
        $model->totalGeneral = $task->getDataValue('totalGeneral');
        $model->isPaid = $task->getDataValue('isPaid');
        $model->remainingAmount = $task->getDataValue('remainingAmount');
        $bankAccountCreationDate = $task->getDataValue('bankAccountCreationDate');
        if ($bankAccountCreationDate) {
            // Try to parse ISO format first (Y-m-d\TH:i:s.000\Z)
            $date = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.000\Z', $bankAccountCreationDate);
            if (!$date) {
                // If that fails, try simple Y-m-d format
                $date = \DateTimeImmutable::createFromFormat('Y-m-d', $bankAccountCreationDate);
            }
            $model->bankAccountCreationDate = $date ?: null;
        } else {
            $model->bankAccountCreationDate = null;
        }
        $model->bankAccount = $task->getDataValue('bankAccount');
        $paymentDate = $task->getDataValue('paymentDate');
        if ($paymentDate) {
            // Try to parse ISO format first (Y-m-d\TH:i:s.000\Z)
            $date = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.000\Z', $paymentDate);
            if (!$date) {
                // If that fails, try simple Y-m-d format
                $date = \DateTimeImmutable::createFromFormat('Y-m-d', $paymentDate);
            }
            $model->paymentDate = $date ?: null;
        } else {
            $model->paymentDate = null;
        }
        $model->roadAxis = $task->getDataValue('roadAxis');
        
        $this->manager->CreateTenant($model);
    }

    private function update(TaskInterface $task): void
    {
        try {
            $externalReferenceId = $task->getExternalReferenceId();
            if (!$externalReferenceId) {
                throw new InvalidActionInputException("ExternalReferenceId is required for updating a PAR");
            }
            
            $par = $this->em->getRepository(Par::class)->findOneBy(['externalReferenceId' => $externalReferenceId]);
            if (!$par) {
                throw new UnavailableDataException("Cannot find PAR with externalReferenceId: {$externalReferenceId}");
            }
            
            $parType = $par->getType();
            
            if ($parType === Par::TYPE_TOMBS) {
                $this->updateTombs($task, $par);
            } elseif ($parType === Par::TYPE_OWNER) {
                $this->updateOwner($task, $par);
            } elseif ($parType === Par::TYPE_TENANT) {
                $this->updateTenant($task, $par);
            } else {
                throw new InvalidActionInputException("Unknown PAR type: {$parType}");
            }
            
            $this->em->persist($par);
            $this->em->flush();
            
            $this->repository->updateTaskStatus($task->getId(), Task::STATUS_TERMINATED, null);
        } catch (\Exception $e) {
            $this->managerRegistry->resetManager();
            $this->repository->updateTaskStatus($task->getId(), Task::STATUS_FAILED, $e->getMessage());
            $this->logger->error('Error processing record: ' . json_encode($task->getData()) . ' - ' . $e->getMessage());
        }
    }
    
    private function updateTombs(TaskInterface $task, Par $par): void
    {
        $task->getDataValue('declarantName') !== null ? $par->setFullname($task->getDataValue('declarantName')) : null;
        $task->getDataValue('declarantSexe') !== null ? $par->setSexe($task->getDataValue('declarantSexe')) : null;
        $task->getDataValue('declarantAge') !== null ? $par->setAge($task->getDataValue('declarantAge')) : null;
        $task->getDataValue('declarantPhone') !== null ? $par->setPhone($task->getDataValue('declarantPhone')) : null;
        $task->getDataValue('village') !== null ? $par->setVillage($task->getDataValue('village')) : null;
        $task->getDataValue('deceasedNameOrDescriptionVault') !== null ? $par->setDeceasedNameOrDescriptionVault($task->getDataValue('deceasedNameOrDescriptionVault')) : null;
        $task->getDataValue('placeOfBirthDeceased') !== null ? $par->setPlaceOfBirthDeceased($task->getDataValue('placeOfBirthDeceased')) : null;
        $task->getExternalReferenceId() !== null ? $par->setExternalReferenceId($task->getExternalReferenceId()) : null;
        if ($task->getDataValue('dateOfBirthDeceased') !== null) {
            $dateOfBirthDeceased = $task->getDataValue('dateOfBirthDeceased');
            // Try to parse ISO format first (Y-m-d\TH:i:s.000\Z)
            $date = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.000\Z', $dateOfBirthDeceased);
            if (!$date) {
                // If that fails, try simple Y-m-d format
                $date = \DateTimeImmutable::createFromFormat('Y-m-d', $dateOfBirthDeceased);
            }
            if ($date) {
                $par->setDateOfBirthDeceased($date);
            }
        }
        $task->getDataValue('deceasedResidence') !== null ? $par->setDeceasedResidence($task->getDataValue('deceasedResidence')) : null;
        $task->getDataValue('spouseName') !== null ? $par->setSpouseName($task->getDataValue('spouseName')) : null;
        $task->getDataValue('measures') !== null ? $par->setMeasures($task->getDataValue('measures')) : null;
        $task->getDataValue('totalGeneral') !== null ? $par->setTotalGeneral($task->getDataValue('totalGeneral')) : null;
        $task->getDataValue('isPaid') !== null ? $par->setIsPaid($task->getDataValue('isPaid')) : null;
        $task->getDataValue('remainingAmount') !== null ? $par->setRemainingAmount($task->getDataValue('remainingAmount')) : null;
        if ($task->getDataValue('bankAccountCreationDate') !== null) {
            $bankAccountCreationDate = $task->getDataValue('bankAccountCreationDate');
            // Try to parse ISO format first (Y-m-d\TH:i:s.000\Z)
            $date = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.000\Z', $bankAccountCreationDate);
            if (!$date) {
                // If that fails, try simple Y-m-d format
                $date = \DateTimeImmutable::createFromFormat('Y-m-d', $bankAccountCreationDate);
            }
            if ($date) {
                $par->setBankAccountCreationDate($date);
            }
        }
        $task->getDataValue('bankAccount') !== null ? $par->setBankAccount($task->getDataValue('bankAccount')) : null;
        if ($task->getDataValue('paymentDate') !== null) {
            $paymentDate = $task->getDataValue('paymentDate');
            // Try to parse ISO format first (Y-m-d\TH:i:s.000\Z)
            $date = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.000\Z', $paymentDate);
            if (!$date) {
                // If that fails, try simple Y-m-d format
                $date = \DateTimeImmutable::createFromFormat('Y-m-d', $paymentDate);
            }
            if ($date) {
                $par->setPaymentDate($date);
            }
        }
        $task->getDataValue('roadAxis') !== null ? $par->setRoadAxis($task->getDataValue('roadAxis')) : null;
    }
    
    private function updateOwner(TaskInterface $task, Par $par): void
    {
        $task->getDataValue('fullname') !== null ? $par->setFullname($task->getDataValue('fullname')) : null;
        $task->getDataValue('sexe') !== null ? $par->setSexe($task->getDataValue('sexe')) : null;
        $task->getDataValue('age') !== null ? $par->setAge($task->getDataValue('age')) : null;
        $task->getDataValue('phone') !== null ? $par->setPhone($task->getDataValue('phone')) : null;
        $task->getDataValue('identificationNumber') !== null ? $par->setIdentificationNumber($task->getDataValue('identificationNumber')) : null;
        $task->getDataValue('formerPap') !== null ? $par->setFormerPap($task->getDataValue('formerPap')) : null;
        $task->getDataValue('kilometerPoint') !== null ? $par->setKilometerPoint($task->getDataValue('kilometerPoint')) : null;
        $task->getDataValue('typeLiability') !== null ? $par->setTypeLiability($task->getDataValue('typeLiability')) : null;
        $task->getDataValue('province') !== null ? $par->setProvince($task->getDataValue('province')) : null;
        $task->getDataValue('territory') !== null ? $par->setTerritory($task->getDataValue('territory')) : null;
        $task->getDataValue('village') !== null ? $par->setVillage($task->getDataValue('village')) : null;
        $task->getExternalReferenceId() !== null ? $par->setExternalReferenceId($task->getExternalReferenceId()) : null;
        $task->getDataValue('longitude') !== null ? $par->setLongitude($task->getDataValue('longitude')) : null;
        $task->getDataValue('latitude') !== null ? $par->setLatitude($task->getDataValue('latitude')) : null;
        $task->getDataValue('referenceCoordinates') !== null ? $par->setReferenceCoordinates($task->getDataValue('referenceCoordinates')) : null;
        $task->getDataValue('orientation') !== null ? $par->setOrientation($task->getDataValue('orientation')) : null;
        $task->getDataValue('vulnerability') !== null ? $par->setVulnerability($task->getDataValue('vulnerability')) : null;
        $task->getDataValue('vulnerabilityType') !== null ? $par->setVulnerabilityType($task->getDataValue('vulnerabilityType')) : null;
        $task->getDataValue('length') !== null ? $par->setLength($task->getDataValue('length')) : null;
        $task->getDataValue('wide') !== null ? $par->setWide($task->getDataValue('wide')) : null;
        $task->getDataValue('areaAllocatedSquareMeters') !== null ? $par->setAreaAllocatedSquareMeters($task->getDataValue('areaAllocatedSquareMeters')) : null;
        $task->getDataValue('cuPerSquareMeter') !== null ? $par->setCuPerSquareMeter($task->getDataValue('cuPerSquareMeter')) : null;
        $task->getDataValue('capitalGain') !== null ? $par->setCapitalGain($task->getDataValue('capitalGain')) : null;
        $task->getDataValue('totalPropertyUsd') !== null ? $par->setTotalPropertyUsd($task->getDataValue('totalPropertyUsd')) : null;
        $task->getDataValue('totalBatisUsd') !== null ? $par->setTotalBatisUsd($task->getDataValue('totalBatisUsd')) : null;
        $task->getDataValue('commercialActivity') !== null ? $par->setCommercialActivity($task->getDataValue('commercialActivity')) : null;
        $task->getDataValue('numberWorkingDaysPerWeek') !== null ? $par->setNumberWorkingDaysPerWeek($task->getDataValue('numberWorkingDaysPerWeek')) : null;
        $task->getDataValue('averageDailyIncome') !== null ? $par->setAverageDailyIncome($task->getDataValue('averageDailyIncome')) : null;
        $task->getDataValue('monthlyIncome') !== null ? $par->setMonthlyIncome($task->getDataValue('monthlyIncome')) : null;
        $task->getDataValue('totalCompensationThreeMonths') !== null ? $par->setTotalCompensationThreeMonths($task->getDataValue('totalCompensationThreeMonths')) : null;
        $task->getDataValue('affectedCultivatedArea') !== null ? $par->setAffectedCultivatedArea($task->getDataValue('affectedCultivatedArea')) : null;
        $task->getDataValue('equivalentUsd') !== null ? $par->setEquivalentUsd($task->getDataValue('equivalentUsd')) : null;
        $task->getDataValue('tree') !== null ? $par->setTree($task->getDataValue('tree')) : null;
        $task->getDataValue('totalFarmIncome') !== null ? $par->setTotalFarmIncome($task->getDataValue('totalFarmIncome')) : null;
        $task->getDataValue('lossRentalIncome') !== null ? $par->setLossRentalIncome($task->getDataValue('lossRentalIncome')) : null;
        $task->getDataValue('movingAssistance') !== null ? $par->setMovingAssistance($task->getDataValue('movingAssistance')) : null;
        $task->getDataValue('assistanceVulnerablePersons') !== null ? $par->setAssistanceVulnerablePersons($task->getDataValue('assistanceVulnerablePersons')) : null;
        $task->getDataValue('noticeAgreementVacatingPremises') !== null ? $par->setNoticeAgreementVacatingPremises($task->getDataValue('noticeAgreementVacatingPremises')) : null;
        $task->getDataValue('totalGeneral') !== null ? $par->setTotalGeneral($task->getDataValue('totalGeneral')) : null;
        $task->getDataValue('isPaid') !== null ? $par->setIsPaid($task->getDataValue('isPaid')) : null;
        $task->getDataValue('remainingAmount') !== null ? $par->setRemainingAmount($task->getDataValue('remainingAmount')) : null;
        if ($task->getDataValue('bankAccountCreationDate') !== null) {
            $bankAccountCreationDate = $task->getDataValue('bankAccountCreationDate');
            // Try to parse ISO format first (Y-m-d\TH:i:s.000\Z)
            $date = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.000\Z', $bankAccountCreationDate);
            if (!$date) {
                // If that fails, try simple Y-m-d format
                $date = \DateTimeImmutable::createFromFormat('Y-m-d', $bankAccountCreationDate);
            }
            if ($date) {
                $par->setBankAccountCreationDate($date);
            }
        }
        $task->getDataValue('bankAccount') !== null ? $par->setBankAccount($task->getDataValue('bankAccount')) : null;
        if ($task->getDataValue('paymentDate') !== null) {
            $paymentDate = $task->getDataValue('paymentDate');
            // Try to parse ISO format first (Y-m-d\TH:i:s.000\Z)
            $date = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.000\Z', $paymentDate);
            if (!$date) {
                // If that fails, try simple Y-m-d format
                $date = \DateTimeImmutable::createFromFormat('Y-m-d', $paymentDate);
            }
            if ($date) {
                $par->setPaymentDate($date);
            }
        }
        $task->getDataValue('roadAxis') !== null ? $par->setRoadAxis($task->getDataValue('roadAxis')) : null;
    }
    
    private function updateTenant(TaskInterface $task, Par $par): void
    {
        $task->getDataValue('fullname') !== null ? $par->setFullname($task->getDataValue('fullname')) : null;
        $task->getDataValue('sexe') !== null ? $par->setSexe($task->getDataValue('sexe')) : null;
        $task->getDataValue('age') !== null ? $par->setAge($task->getDataValue('age')) : null;
        $task->getDataValue('phone') !== null ? $par->setPhone($task->getDataValue('phone')) : null;
        $task->getDataValue('identificationNumber') !== null ? $par->setIdentificationNumber($task->getDataValue('identificationNumber')) : null;
        $task->getDataValue('formerPap') !== null ? $par->setFormerPap($task->getDataValue('formerPap')) : null;
        $task->getDataValue('province') !== null ? $par->setProvince($task->getDataValue('province')) : null;
        $task->getDataValue('territory') !== null ? $par->setTerritory($task->getDataValue('territory')) : null;
        $task->getDataValue('village') !== null ? $par->setVillage($task->getDataValue('village')) : null;
        $task->getExternalReferenceId() !== null ? $par->setExternalReferenceId($task->getExternalReferenceId()) : null;
        $task->getDataValue('longitude') !== null ? $par->setLongitude($task->getDataValue('longitude')) : null;
        $task->getDataValue('latitude') !== null ? $par->setLatitude($task->getDataValue('latitude')) : null;
        $task->getDataValue('orientation') !== null ? $par->setOrientation($task->getDataValue('orientation')) : null;
        $task->getDataValue('vulnerability') !== null ? $par->setVulnerability($task->getDataValue('vulnerability')) : null;
        $task->getDataValue('vulnerabilityType') !== null ? $par->setVulnerabilityType($task->getDataValue('vulnerabilityType')) : null;
        $task->getDataValue('length') !== null ? $par->setLength($task->getDataValue('length')) : null;
        $task->getDataValue('wide') !== null ? $par->setWide($task->getDataValue('wide')) : null;
        $task->getDataValue('areaAllocatedSquareMeters') !== null ? $par->setAreaAllocatedSquareMeters($task->getDataValue('areaAllocatedSquareMeters')) : null;
        $task->getDataValue('cuPerSquareMeter') !== null ? $par->setCuPerSquareMeter($task->getDataValue('cuPerSquareMeter')) : null;
        $task->getDataValue('capitalGain') !== null ? $par->setCapitalGain($task->getDataValue('capitalGain')) : null;
        $task->getDataValue('totalPropertyUsd') !== null ? $par->setTotalPropertyUsd($task->getDataValue('totalPropertyUsd')) : null;
        $task->getDataValue('totalBatisUsd') !== null ? $par->setTotalBatisUsd($task->getDataValue('totalBatisUsd')) : null;
        $task->getDataValue('commercialActivity') !== null ? $par->setCommercialActivity($task->getDataValue('commercialActivity')) : null;
        $task->getDataValue('numberWorkingDaysPerWeek') !== null ? $par->setNumberWorkingDaysPerWeek($task->getDataValue('numberWorkingDaysPerWeek')) : null;
        $task->getDataValue('averageDailyIncome') !== null ? $par->setAverageDailyIncome($task->getDataValue('averageDailyIncome')) : null;
        $task->getDataValue('monthlyIncome') !== null ? $par->setMonthlyIncome($task->getDataValue('monthlyIncome')) : null;
        $task->getDataValue('totalCompensationThreeMonths') !== null ? $par->setTotalCompensationThreeMonths($task->getDataValue('totalCompensationThreeMonths')) : null;
        $task->getDataValue('affectedCultivatedArea') !== null ? $par->setAffectedCultivatedArea($task->getDataValue('affectedCultivatedArea')) : null;
        $task->getDataValue('equivalentUsd') !== null ? $par->setEquivalentUsd($task->getDataValue('equivalentUsd')) : null;
        $task->getDataValue('tree') !== null ? $par->setTree($task->getDataValue('tree')) : null;
        $task->getDataValue('totalFarmIncome') !== null ? $par->setTotalFarmIncome($task->getDataValue('totalFarmIncome')) : null;
        $task->getDataValue('lossRentalIncome') !== null ? $par->setLossRentalIncome($task->getDataValue('lossRentalIncome')) : null;
        $task->getDataValue('movingAssistance') !== null ? $par->setMovingAssistance($task->getDataValue('movingAssistance')) : null;
        $task->getDataValue('assistanceVulnerablePersons') !== null ? $par->setAssistanceVulnerablePersons($task->getDataValue('assistanceVulnerablePersons')) : null;
        $task->getDataValue('rentalGuaranteeAssistance') !== null ? $par->setRentalGuaranteeAssistance($task->getDataValue('rentalGuaranteeAssistance')) : null;
        $task->getDataValue('noticeAgreementVacatingPremises') !== null ? $par->setNoticeAgreementVacatingPremises($task->getDataValue('noticeAgreementVacatingPremises')) : null;
        $task->getDataValue('tenantMonthlyRent') !== null ? $par->setTenantMonthlyRent($task->getDataValue('tenantMonthlyRent')) : null;
        $task->getDataValue('lessorName') !== null ? $par->setLessorName($task->getDataValue('lessorName')) : null;
        $task->getDataValue('totalRent') !== null ? $par->setTotalRent($task->getDataValue('totalRent')) : null;
        $task->getDataValue('totalLossEmploymentIncome') !== null ? $par->setTotalLossEmploymentIncome($task->getDataValue('totalLossEmploymentIncome')) : null;
        $task->getDataValue('totalLossBusinessIncome') !== null ? $par->setTotalLossBusinessIncome($task->getDataValue('totalLossBusinessIncome')) : null;
        $task->getDataValue('kilometerPoint') !== null ? $par->setKilometerPoint($task->getDataValue('kilometerPoint')) : null;
        $task->getDataValue('category') !== null ? $par->setCategory($task->getDataValue('category')) : null;
        $task->getDataValue('totalGeneral') !== null ? $par->setTotalGeneral($task->getDataValue('totalGeneral')) : null;
        $task->getDataValue('isPaid') !== null ? $par->setIsPaid($task->getDataValue('isPaid')) : null;
        $task->getDataValue('remainingAmount') !== null ? $par->setRemainingAmount($task->getDataValue('remainingAmount')) : null;
        if ($task->getDataValue('bankAccountCreationDate') !== null) {
            $bankAccountCreationDate = $task->getDataValue('bankAccountCreationDate');
            // Try to parse ISO format first (Y-m-d\TH:i:s.000\Z)
            $date = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.000\Z', $bankAccountCreationDate);
            if (!$date) {
                // If that fails, try simple Y-m-d format
                $date = \DateTimeImmutable::createFromFormat('Y-m-d', $bankAccountCreationDate);
            }
            if ($date) {
                $par->setBankAccountCreationDate($date);
            }
        }
        $task->getDataValue('bankAccount') !== null ? $par->setBankAccount($task->getDataValue('bankAccount')) : null;
        if ($task->getDataValue('paymentDate') !== null) {
            $paymentDate = $task->getDataValue('paymentDate');
            // Try to parse ISO format first (Y-m-d\TH:i:s.000\Z)
            $date = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.000\Z', $paymentDate);
            if (!$date) {
                // If that fails, try simple Y-m-d format
                $date = \DateTimeImmutable::createFromFormat('Y-m-d', $paymentDate);
            }
            if ($date) {
                $par->setPaymentDate($date);
            }
        }
        $task->getDataValue('roadAxis') !== null ? $par->setRoadAxis($task->getDataValue('roadAxis')) : null;
    }
}