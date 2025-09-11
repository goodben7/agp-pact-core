<?php

namespace App\MessageHandler\Command;

use App\Entity\Par;
use App\Model\NewOwnerModel;
use App\Manager\ParManager;
use Psr\Log\LoggerInterface;
use App\Message\Command\CreateOwnerCommand;
use App\Message\Command\CommandHandlerInterface;

class CreateOwnerCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private ParManager $manager
    ) {
    }

    /**
     * Summary of __invoke
     * @param \App\Message\Command\CreateOwnerCommand $command
     * @throws \Exception
     * @return Par
     */
    public function __invoke(CreateOwnerCommand $command): Par
    {
        try {
            $model = new NewOwnerModel(
                $command->code,
                $command->fullname,
                $command->sexe,
                $command->age,
                $command->phone,
                $command->identificationNumber,
                $command->formerPap,
                $command->kilometerPoint,
                $command->typeLiability,
                $command->province,
                $command->territory,
                $command->village,
                $command->longitude,
                $command->latitude,
                $command->referenceCoordinates,
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
                $command->noticeAgreementVacatingPremises,
                $command->totalGeneral
            );

            return $this->manager->CreateOwner($model);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new \Exception('Error in CreateOwnerCommandHandler: ' . $e->getMessage(), 0, $e);
        }
    }
}