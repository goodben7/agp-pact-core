<?php

namespace App\MessageHandler\Command;

use App\Entity\Par;
use App\Model\NewTenantModel;
use App\Manager\ParManager;
use Psr\Log\LoggerInterface;
use App\Message\Command\CreateTenantCommand;
use App\Message\Command\CommandHandlerInterface;

class CreateTenantCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private ParManager $manager
    ) {
    }

    /**
     * Summary of __invoke
     * @param \App\Message\Command\CreateTenantCommand $command
     * @throws \Exception
     * @return Par
     */
    public function __invoke(CreateTenantCommand $command): Par
    {
        try {
            $model = new NewTenantModel(
                $command->code,
                $command->fullname,
                $command->sexe,
                $command->age,
                $command->phone,
                $command->identificationNumber,
                $command->formerPap,
                $command->province,
                $command->territory,
                $command->village,
                $command->longitude,
                $command->latitude,
                $command->orientation,
                $command->vulnerability,
                $command->vulnerabilityType,
                $command->length,
                $command->wide,
                $command->areaAllocatedSquareMeters,
                $command->cuPerSquareMeter,
                $command->capitalGain,
                $command->totalPropertyUsd,
                $command->totalBatisUsd,
                $command->commercialActivity,
                $command->numberWorkingDaysPerWeek,
                $command->averageDailyIncome,
                $command->monthlyIncome,
                $command->totalCompensationThreeMonths,
                $command->affectedCultivatedArea,
                $command->equivalentUsd,
                $command->tree,
                $command->totalFarmIncome,
                $command->lossRentalIncome,
                $command->movingAssistance,
                $command->assistanceVulnerablePersons,
                $command->rentalGuaranteeAssistance,
                $command->noticeAgreementVacatingPremises,
                $command->tenantMonthlyRent,
                $command->lessorName,
                $command->totalRent,
                $command->totalLossEmploymentIncome,
                $command->totalLossBusinessIncome,
                $command->kilometerPoint,
                $command->category,
                $command->totalGeneral
            );

            return $this->manager->CreateTenant($model);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new \Exception('Error in CreateTenantCommandHandler: ' . $e->getMessage(), 0, $e);
        }
    }
}