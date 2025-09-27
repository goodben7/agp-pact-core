<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Message\Command\CommandBusInterface;
use App\Message\Command\CreateTombsCommand;

class CreateTombsProcessor implements ProcessorInterface
{
    public function __construct(private CommandBusInterface $bus)
    {
    }

    /**
     * @param \App\Dto\CreateTombsDto $data 
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $command = new CreateTombsCommand(
            $data->code,
            $data->declarantName,
            $data->declarantSexe,
            $data->declarantAge,
            $data->declarantPhone,
            $data->village,
            $data->deceasedNameOrDescriptionVault,
            $data->placeOfBirthDeceased,
            $data->dateOfBirthDeceased,
            $data->deceasedResidence,
            $data->spouseName,
            $data->measures,
            $data->totalGeneral,
            $data->isPaid,
            $data->remainingAmount,
            $data->bankAccountCreationDate,
            $data->bankAccount,
            $data->paymentDate
        );

        return $this->bus->dispatch($command);
    }
}
