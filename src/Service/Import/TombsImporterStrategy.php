<?php

namespace App\Service\Import\Strategy;

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

                if ($entityProperty === 'declarantAge') {
                    $value = (int) $value;
                } elseif ($entityProperty === 'dateOfBirthDeceased' && isset($columnMap['format'])) {
                    $date = \DateTimeImmutable::createFromFormat($columnMap['format'], $value);
                    if (!$date) {
                        throw new \InvalidArgumentException("Format de date invalide pour '{$fileHeader}'. Attendu : '{$columnMap['format']}'. Reçu : '{$value}'.");
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
            totalGeneral: $dto->totalGeneral
        );
        $this->messageBus->dispatch($command);
    }

    public function supports(string $entityType): bool
    {
        return self::ENTITY_TYPE === $entityType;
    }
}