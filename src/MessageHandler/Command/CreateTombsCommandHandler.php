<?php

namespace App\MessageHandler\Command;

use App\Entity\Par;
use App\Model\NewTombsModel;
use App\Manager\ParManager;
use Psr\Log\LoggerInterface;
use App\Message\Command\CreateTombsCommand;
use App\Message\Command\CommandHandlerInterface;

class CreateTombsCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private ParManager $manager
    ) {
    }

    /**
     * Summary of __invoke
     * @param \App\Message\Command\CreateTombsCommand $command
     * @throws \Exception
     * @return Par
     */
    public function __invoke(CreateTombsCommand $command): Par
    {
        try {
            $model = new NewTombsModel(
                $command->code,
                $command->declarantName,
                $command->declarantSexe,
                $command->declarantAge,
                $command->declarantPhone,
                $command->village,
                $command->deceasedNameOrDescriptionVault,
                $command->placeOfBirthDeceased,
                $command->dateOfBirthDeceased,
                $command->deceasedResidence,
                $command->spouseName,
                $command->measures,
                $command->totalGeneral
            );

            return $this->manager->CreateTombs($model);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new \Exception('Error in CreateTombsCommandHandler: ' . $e->getMessage(), 0, $e);
        }
    }
}