<?php

namespace App\Manager;

use App\Entity\Par;
use App\Model\NewOwnerModel;
use App\Model\NewTombsModel;
use App\Model\NewTenantModel;
use App\Entity\PaymentHistory;
use App\Model\NewPaymentHistoryModel;
use App\Message\Query\QueryBusInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Message\Query\GetLocationDetails;
use App\Message\Query\GetRoadAxisDetails;
use App\Constant\GeneralParameterCategory;
use App\Exception\UnavailableDataException;
use App\Exception\InvalidActionInputException;
use App\Message\Query\GetGeneralParameterDetails;

class ParManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private QueryBusInterface $queries,
    )
    {
    }
    
    
    public function recordPayment(NewPaymentHistoryModel $model): PaymentHistory
    {
        $par = $this->em->getRepository(Par::class)->find($model->parId);
        if (!$par) {
            throw new \InvalidArgumentException("PAR non trouvé avec l'ID: {$model->parId}");
        }
        
        $paymentHistory = new PaymentHistory();
        $paymentHistory->setPar($par);
        $paymentHistory->setPaymentDate($model->paymentDate);
        $paymentHistory->setAmount($model->amount);
        $paymentHistory->setTransactionReference($model->transactionReference);
        $paymentHistory->setPaymentMethod($model->paymentMethod);
        $paymentHistory->setNotes($model->notes);
        $paymentHistory->setCreatedAt(new \DateTimeImmutable());
        
        $totalGeneral = (float) $par->getTotalGeneral() ?: 0;
        $remainingAmount = (float) $par->getRemainingAmount() ?: $totalGeneral;
        $paidAmount = (float) $model->amount;
        
        $newRemainingAmount = max(0, $remainingAmount - $paidAmount);
        $par->setRemainingAmount((string) $newRemainingAmount);
        
        if ($newRemainingAmount <= 0) {
            $par->setIsPaid(true);
        } else {
            $par->setIsPaid(false);
        }
        
        if ($par->getPaymentDate() === null) {
            $par->setPaymentDate($model->paymentDate);
        }
        
        $this->em->persist($paymentHistory);
        $this->em->persist($par);
        $this->em->flush();
        
        return $paymentHistory;
    }

    public function CreateTombs(NewTombsModel $model): Par
    {
        if ($model->roadAxis !== null) {
            $roadAxis = $this->queries->ask(new GetRoadAxisDetails($model->roadAxis));
            if ($roadAxis === null) {
                throw new UnavailableDataException("cannot find road axis with id : {$model->roadAxis}");
            }
        }

        if ($model->declarantSexe !== null) {
            $sexe = $this->queries->ask(new GetGeneralParameterDetails($model->declarantSexe, GeneralParameterCategory::GENDER));
            if ($sexe == null) {
                throw new UnavailableDataException("cannot find sexe with id : {$model->declarantSexe}");
            }
        }

        if ($model->village !== null) {
            $village = $this->queries->ask(new GetLocationDetails($model->village));
            if ($village == null) {
                throw new UnavailableDataException("cannot find village with id : {$model->village}");
            }
        }

        if ($model->placeOfBirthDeceased !== null) {
            $placeOfBirthDeceased = $this->queries->ask(new GetLocationDetails($model->placeOfBirthDeceased));
            if ($placeOfBirthDeceased == null) {
                throw new UnavailableDataException("cannot find place of birth deceased with id : {$model->placeOfBirthDeceased}");
            }
        }

        if ($model->deceasedResidence !== null) {
            $deceasedResidence = $this->queries->ask(new GetLocationDetails($model->deceasedResidence));
            if ($deceasedResidence == null) {
                throw new UnavailableDataException("cannot find deceased residence with id : {$model->deceasedResidence}");
            }
        }

        $par = new Par();
        
        $par->setType(Par::TYPE_TOMBS);
        $par->setCode($model->code);
        $par->setFullname($model->declarantName);
        $par->setSexe($model->declarantSexe);
        $par->setAge($model->declarantAge);
        $par->setPhone($model->declarantPhone);
        $par->setVillage($model->village);
        $par->setDeceasedNameOrDescriptionVault($model->deceasedNameOrDescriptionVault);
        $par->setPlaceOfBirthDeceased($model->placeOfBirthDeceased);
        $par->setDateOfBirthDeceased($model->dateOfBirthDeceased);
        $par->setDeceasedResidence($model->deceasedResidence);
        $par->setSpouseName($model->spouseName);
        $par->setMeasures($model->measures);
        $par->setTotalGeneral($model->totalGeneral);
        $par->setIsPaid($model->isPaid);
        $par->setRemainingAmount($model->remainingAmount);
        $par->setBankAccountCreationDate($model->bankAccountCreationDate);
        $par->setBankAccount($model->bankAccount);
        $par->setPaymentDate($model->paymentDate);
        $par->setCreatedAt(new \DateTimeImmutable());
        $par->setRoadAxis($model->roadAxis);
        
        $this->em->persist($par);
        $this->em->flush();
        
        return $par;
    }

    public function CreateOwner(NewOwnerModel $model): Par
    {
        if ($model->roadAxis !== null) {
            $roadAxis = $this->queries->ask(new GetRoadAxisDetails($model->roadAxis));
            if ($roadAxis === null) {
                throw new UnavailableDataException("cannot find road axis with id : {$model->roadAxis}");
            }
        }

        if ($model->sexe !== null) {
            $sexe = $this->queries->ask(new GetGeneralParameterDetails($model->sexe, GeneralParameterCategory::GENDER));
            if ($sexe == null ){
                throw new UnavailableDataException("cannot find sexe with id : {$model->sexe}");
            }
        }

        if ($model->province !== null) {
            $province = $this->queries->ask(new GetLocationDetails($model->province));
            if ($province == null ){
                throw new UnavailableDataException("cannot find province with id : {$model->province}");
            }
        }

        if ($model->territory !== null) {
            $territory = $this->queries->ask(new GetLocationDetails($model->territory));
            if ($territory == null ){
                throw new UnavailableDataException("cannot find territory with id : {$model->territory}");
            }
        }

        if ($model->village !== null) {
            $village = $this->queries->ask(new GetLocationDetails($model->village));
            if ($village == null ){
                throw new UnavailableDataException("cannot find village with id : {$model->village}");
            }
        }

        if ($model->vulnerabilityType !== null) {
            $vulnerabilityType = $this->queries->ask(new GetGeneralParameterDetails($model->vulnerabilityType, GeneralParameterCategory::VULNERABILITY_DEGREE));
            if ($vulnerabilityType == null ){
                throw new UnavailableDataException("cannot find vulnerability type with id : {$model->vulnerabilityType}");
            }
        }

        $par = new Par();
        
        $par->setType(Par::TYPE_OWNER);
        $par->setCode($model->code);
        $par->setFullname($model->fullname);
        $par->setSexe($model->sexe);
        $par->setAge($model->age);
        $par->setPhone($model->phone);
        $par->setIdentificationNumber($model->identificationNumber);
        $par->setFormerPap($model->formerPap);
        $par->setKilometerPoint($model->kilometerPoint);
        $par->setTypeLiability($model->typeLiability);
        $par->setProvince($model->province);
        $par->setTerritory($model->territory);
        $par->setVillage($model->village);
        $par->setLongitude($model->longitude);
        $par->setLatitude($model->latitude);
        $par->setReferenceCoordinates($model->referenceCoordinates);
        $par->setOrientation($model->orientation);
        $par->setVulnerability($model->vulnerability);
        $par->setVulnerabilityType($model->vulnerabilityType);
        $par->setLength($model->length);
        $par->setWide($model->wide);
        $par->setAreaAllocatedSquareMeters($model->areaAllocatedSquareMeters);
        $par->setCuPerSquareMeter($model->cuPerSquareMeter);
        $par->setCapitalGain($model->capitalGain);
        $par->setTotalPropertyUsd($model->totalPropertyUsd);
        $par->setTotalBatisUsd($model->totalBatisUsd);
        $par->setCommercialActivity($model->commercialActivity);
        $par->setNumberWorkingDaysPerWeek($model->numberWorkingDaysPerWeek);
        $par->setAverageDailyIncome($model->averageDailyIncome);
        $par->setMonthlyIncome($model->monthlyIncome);
        $par->setTotalCompensationThreeMonths($model->totalCompensationThreeMonths);
        $par->setAffectedCultivatedArea($model->affectedCultivatedArea);
        $par->setEquivalentUsd($model->equivalentUsd);
        $par->setTree($model->tree);
        $par->setTotalFarmIncome($model->totalFarmIncome);
        $par->setLossRentalIncome($model->lossRentalIncome);
        $par->setMovingAssistance($model->movingAssistance);
        $par->setAssistanceVulnerablePersons($model->assistanceVulnerablePersons);
        $par->setNoticeAgreementVacatingPremises($model->noticeAgreementVacatingPremises);
        $par->setTotalGeneral($model->totalGeneral);
        $par->setIsPaid($model->isPaid);
        $par->setRemainingAmount($model->remainingAmount);
        $par->setBankAccountCreationDate($model->bankAccountCreationDate);
        $par->setBankAccount($model->bankAccount);
        $par->setPaymentDate($model->paymentDate);
        $par->setCreatedAt(new \DateTimeImmutable());
        $par->setRoadAxis($model->roadAxis);
        
        $this->em->persist($par);
        $this->em->flush();
        
        return $par;
    }

    public function CreateTenant(NewTenantModel $model): Par
    {
        if ($model->roadAxis !== null) {
            $roadAxis = $this->queries->ask(new GetRoadAxisDetails($model->roadAxis));
            if ($roadAxis === null) {
                throw new UnavailableDataException("cannot find road axis with id : {$model->roadAxis}");
            }
        }

        if ($model->sexe !== null) {
            $sexe = $this->queries->ask(new GetGeneralParameterDetails($model->sexe, GeneralParameterCategory::GENDER));
            if ($sexe == null) {
                throw new UnavailableDataException("cannot find sexe with id : {$model->sexe}");
            }
        }

        if ($model->province !== null) {
            $province = $this->queries->ask(new GetLocationDetails($model->province));
            if ($province == null) {
                throw new UnavailableDataException("cannot find province with id : {$model->province}");
            }
        }

        if ($model->territory !== null) {
            $territory = $this->queries->ask(new GetLocationDetails($model->territory));
            if ($territory == null) {
                throw new UnavailableDataException("cannot find territory with id : {$model->territory}");
            }
        }

        if ($model->village !== null) {
            $village = $this->queries->ask(new GetLocationDetails($model->village));
            if ($village == null) {
                throw new UnavailableDataException("cannot find village with id : {$model->village}");
            }
        }

        if ($model->vulnerabilityType !== null) {
            $vulnerabilityType = $this->queries->ask(new GetGeneralParameterDetails($model->vulnerabilityType, GeneralParameterCategory::VULNERABILITY_DEGREE));
            if ($vulnerabilityType == null) {
                throw new UnavailableDataException("cannot find vulnerability type with id : {$model->vulnerabilityType}");
            }
        }

        $par = new Par();
        
        $par->setType(Par::TYPE_TENANT);
        $par->setCode($model->code);
        $par->setFullname($model->fullname);
        $par->setSexe($model->sexe);
        $par->setAge($model->age);
        $par->setPhone($model->phone);
        $par->setIdentificationNumber($model->identificationNumber);
        $par->setFormerPap($model->formerPap);
        $par->setKilometerPoint($model->kilometerPoint);
        $par->setProvince($model->province);
        $par->setTerritory($model->territory);
        $par->setVillage($model->village);
        $par->setLongitude($model->longitude);
        $par->setLatitude($model->latitude);
        $par->setOrientation($model->orientation);
        $par->setVulnerability($model->vulnerability);
        $par->setVulnerabilityType($model->vulnerabilityType);
        $par->setLength($model->length);
        $par->setWide($model->wide);
        $par->setAreaAllocatedSquareMeters($model->areaAllocatedSquareMeters);
        $par->setCuPerSquareMeter($model->cuPerSquareMeter);
        $par->setCapitalGain($model->capitalGain);
        $par->setTotalPropertyUsd($model->totalPropertyUsd);
        $par->setTotalBatisUsd($model->totalBatisUsd);
        $par->setCommercialActivity($model->commercialActivity);
        $par->setNumberWorkingDaysPerWeek($model->numberWorkingDaysPerWeek);
        $par->setAverageDailyIncome($model->averageDailyIncome);
        $par->setMonthlyIncome($model->monthlyIncome);
        $par->setTotalCompensationThreeMonths($model->totalCompensationThreeMonths);
        $par->setAffectedCultivatedArea($model->affectedCultivatedArea);
        $par->setEquivalentUsd($model->equivalentUsd);
        $par->setTree($model->tree);
        $par->setTotalFarmIncome($model->totalFarmIncome);
        $par->setLossRentalIncome($model->lossRentalIncome);
        $par->setMovingAssistance($model->movingAssistance);
        $par->setAssistanceVulnerablePersons($model->assistanceVulnerablePersons);
        $par->setRentalGuaranteeAssistance($model->rentalGuaranteeAssistance);
        $par->setNoticeAgreementVacatingPremises($model->noticeAgreementVacatingPremises);
        $par->setTenantMonthlyRent($model->tenantMonthlyRent);
        $par->setLessorName($model->lessorName);
        $par->setTotalRent($model->totalRent);
        $par->setTotalLossEmploymentIncome($model->totalLossEmploymentIncome);
        $par->setTotalLossBusinessIncome($model->totalLossBusinessIncome);
        $par->setKilometerPoint($model->kilometerPoint);
        $par->setCategory($model->category);
        $par->setTotalGeneral($model->totalGeneral);
        $par->setIsPaid($model->isPaid);
        $par->setRemainingAmount($model->remainingAmount);
        $par->setBankAccountCreationDate($model->bankAccountCreationDate);
        $par->setBankAccount($model->bankAccount);
        $par->setPaymentDate($model->paymentDate);
        $par->setCreatedAt(new \DateTimeImmutable());
        $par->setRoadAxis($model->roadAxis);
        
        $this->em->persist($par);
        $this->em->flush();
        
        return $par;
    }

    public function validate(Par $par) : Par
    {
        if ($par->getStatus() !== Par::STATUS_PENDING) {
            throw new InvalidActionInputException('Action not allowed : invalid par state'); 
        }


        $par->setStatus(Par::STATUS_VALIDATED);
        $par->setValidatedAt(new \DateTimeImmutable('now'));

        $this->em->persist($par);
        $this->em->flush();


        return $par; 
    }
}