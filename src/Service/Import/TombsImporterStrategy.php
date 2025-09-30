<?php

namespace App\Service\Import;

use App\Dto\CreateTombsDto;
use App\Entity\ImportItem;
use App\Message\Command\CreateTombsCommand;
use App\Service\ImporterStrategyInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class TombsImporterStrategy implements ImporterStrategyInterface
{
    private const ENTITY_TYPE = 'tombs';

    public function __construct(
        private PropertyAccessorInterface $propertyAccessor,
        private ValidatorInterface $validator,
        private MessageBusInterface $messageBus
    )
    {
    }

    public function process(ImportItem $item): void
    {
        $mappingConfig = $item->getBatch()->getMapping()->getMappingConfiguration();
        $rowData = $item->getRowData();

        $dto = new CreateTombsDto();

        foreach ($mappingConfig['columns'] as $columnMap) {
            $fileHeader = $columnMap['fileHeader'];
            $entityProperty = $columnMap['entityProperty'];

            if (isset($rowData[$fileHeader]) && !empty(trim($rowData[$fileHeader]))) {
                $value = trim($rowData[$fileHeader]);

                // Gestion des types de données
                if ($entityProperty === 'declarantAge') {
                    $value = (int) $value;
                } elseif (in_array($entityProperty, ['isPaid'])) {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                } elseif (in_array($entityProperty, ['dateOfBirthDeceased', 'bankAccountCreationDate', 'paymentDate'])) {
                    $format = $columnMap['format'] ?? 'Y-m-d';
                    $date = \DateTimeImmutable::createFromFormat($format, $value);
                    if (!$date) {
                        throw new \InvalidArgumentException("Format de date invalide pour '{$fileHeader}'. Attendu : '{$format}'. Reçu : '{$value}'.");
                    }
                    $value = $date;
                }

                $this->propertyAccessor->setValue($dto, $entityProperty, $value);
            }
        }

        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            $errorMessages = [];
            foreach ($violations as $violation) {
                $errorMessages[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            throw new \InvalidArgumentException(implode("\n", $errorMessages));
        }

        $command = new CreateTombsCommand(
            code: $dto->code,
            declarantName: $dto->declarantName,
            declarantSexe: $dto->declarantSexe,
            declarantAge: $dto->declarantAge,
            declarantPhone: $dto->declarantPhone,
            village: $dto->village,
            deceasedNameOrDescriptionVault: $dto->deceasedNameOrDescriptionVault,
            placeOfBirthDeceased: $dto->placeOfBirthDeceased,
            dateOfBirthDeceased: $dto->dateOfBirthDeceased,
            deceasedResidence: $dto->deceasedResidence,
            spouseName: $dto->spouseName,
            measures: $dto->measures,
            totalGeneral: $dto->totalGeneral,
            isPaid: $dto->isPaid,
            remainingAmount: $dto->remainingAmount,
            bankAccountCreationDate: $dto->bankAccountCreationDate,
            bankAccount: $dto->bankAccount,
            paymentDate: $dto->paymentDate,
            roadAxis: $dto->roadAxis
        );
        $this->messageBus->dispatch($command);
    }

    public function supports(string $entityType): bool
    {
        return self::ENTITY_TYPE === $entityType;
    }
}