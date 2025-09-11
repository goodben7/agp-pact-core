<?php

namespace App\Service\Import\Strategy;

use App\Entity\ImportItem;
use App\Manager\PapManager;
use App\Service\ImporterStrategyInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class PapImporterStrategy implements ImporterStrategyInterface
{
    private const ENTITY_TYPE = 'pap';

    public function __construct(
        private PapManager            $manager,
        private PropertyAccessorInterface $propertyAccessor,
        private ValidatorInterface        $validator
    )
    {
    }

    public function process(ImportItem $item): void
    {
        $mappingConfig = $item->getBatch()->getMapping()->getMappingConfiguration();
        $rowData = $item->getRowData();

        // $dto = new CreateStudentDto();

        // foreach ($mappingConfig['columns'] as $columnMap) {
        //     $fileHeader = $columnMap['fileHeader'];
        //     $entityProperty = $columnMap['entityProperty'];

        //     if (isset($rowData[$fileHeader]) && !empty(trim($rowData[$fileHeader]))) {
        //         $value = trim($rowData[$fileHeader]);

        //         if ($entityProperty === 'dateOfBirth' && isset($columnMap['format'])) {
        //             $date = \DateTime::createFromFormat($columnMap['format'], $value);
        //             if (!$date)
        //                 throw new \InvalidArgumentException("Format de date invalide pour '{$fileHeader}'. Attendu : '{$columnMap['format']}'. Reçu : '{$value}'.");
        //             $value = $date;
        //         }

        //         $this->propertyAccessor->setValue($dto, $entityProperty, $value);
        //     }
        // }

        // $violations = $this->validator->validate($dto);
        // if (count($violations) > 0) {
        //     $errorMessages = [];
        //     foreach ($violations as $violation) {
        //         $errorMessages[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
        //     }
        //     throw new \InvalidArgumentException(implode("\n", $errorMessages));
        // }
    }

    public function supports(string $entityType): bool
    {
        return self::ENTITY_TYPE === $entityType;
    }
}