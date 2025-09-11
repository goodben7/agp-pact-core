<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Message\Command\CommandBusInterface;
use App\Message\Command\CreateTenantCommand;

class CreateTenantProcessor implements ProcessorInterface
{
    public function __construct(private CommandBusInterface $bus)
    {
    }

    /**
     * @param \App\Dto\CreateTenantDto $data 
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $command = new CreateTenantCommand(
            $data->code,
            $data->fullname,
            $data->sexe,
            $data->age,
            $data->phone,
            $data->identificationNumber,
            $data->formerPap,
            $data->province,
            $data->territory,
            $data->village,
            $data->longitude,
            $data->latitude,
            $data->orientation,
            $data->vulnerability,
            $data->vulnerabilityType,
            $data->length,
            $data->wide,
            $data->areaAllocatedSquareMeters,
            $data->cuPerSquareMeter,
            $data->capitalGain,
            $data->totalPropertyUsd,
            $data->totalBatisUsd,
            $data->commercialActivity,
            $data->numberWorkingDaysPerWeek,
            $data->averageDailyIncome,
            $data->monthlyIncome,
            $data->totalCompensationThreeMonths,
            $data->affectedCultivatedArea,
            $data->equivalentUsd,
            $data->tree,
            $data->totalFarmIncome,
            $data->lossRentalIncome,
            $data->movingAssistance,
            $data->assistanceVulnerablePersons,
            $data->rentalGuaranteeAssistance,
            $data->noticeAgreementVacatingPremises,
            $data->tenantMonthlyRent,
            $data->lessorName,
            $data->totalRent,
            $data->totalLossEmploymentIncome,
            $data->totalLossBusinessIncome,
            $data->kilometerPoint,
            $data->category,
            $data->totalGeneral
        );

        return $this->bus->dispatch($command);
    }
}