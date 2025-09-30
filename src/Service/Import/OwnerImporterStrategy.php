<?php

namespace App\Service\Import;

use App\Dto\CreateOwnerDto;
use App\Entity\ImportItem;
use App\Message\Command\CreateOwnerCommand;
use App\Service\ImporterStrategyInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class OwnerImporterStrategy implements ImporterStrategyInterface
{
    private const ENTITY_TYPE = 'owner';

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

        $dto = new CreateOwnerDto();

        foreach ($mappingConfig['columns'] as $columnMap) {
            $fileHeader = $columnMap['fileHeader'];
            $entityProperty = $columnMap['entityProperty'];

            if (isset($rowData[$fileHeader]) && !empty(trim($rowData[$fileHeader]))) {
                $value = trim($rowData[$fileHeader]);

                // Gestion des types de données
                if (in_array($entityProperty, ['age', 'numberWorkingDaysPerWeek'])) {
                    $value = (int) $value;
                } elseif (in_array($entityProperty, ['formerPap', 'vulnerability', 'noticeAgreementVacatingPremises', 'isPaid'])) {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                } elseif (in_array($entityProperty, ['bankAccountCreationDate', 'paymentDate'])) {
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

        $command = new CreateOwnerCommand(
            code: $dto->code,
            fullname: $dto->fullname,
            sexe: $dto->sexe,
            age: $dto->age,
            phone: $dto->phone,
            identificationNumber: $dto->identificationNumber,
            formerPap: $dto->formerPap,
            kilometerPoint: $dto->kilometerPoint,
            typeLiability: $dto->typeLiability,
            province: $dto->province,
            territory: $dto->territory,
            village: $dto->village,
            longitude: $dto->longitude,
            latitude: $dto->latitude,
            referenceCoordinates: $dto->referenceCoordinates,
            orientation: $dto->orientation,
            vulnerability: $dto->vulnerability,
            vulnerabilityType: $dto->vulnerabilityType,
            length: $dto->length,
            wide: $dto->wide,
            areaAllocatedSquareMeters: $dto->areaAllocatedSquareMeters,
            cuPerSquareMeter: $dto->cuPerSquareMeter,
            capitalGain: $dto->capitalGain,
            totalPropertyUsd: $dto->totalPropertyUsd,
            totalBatisUsd: $dto->totalBatisUsd,
            commercialActivity: $dto->commercialActivity,
            numberWorkingDaysPerWeek: $dto->numberWorkingDaysPerWeek,
            averageDailyIncome: $dto->averageDailyIncome,
            monthlyIncome: $dto->monthlyIncome,
            totalCompensationThreeMonths: $dto->totalCompensationThreeMonths,
            affectedCultivatedArea: $dto->affectedCultivatedArea,
            equivalentUsd: $dto->equivalentUsd,
            tree: $dto->tree,
            totalFarmIncome: $dto->totalFarmIncome,
            lossRentalIncome: $dto->lossRentalIncome,
            movingAssistance: $dto->movingAssistance,
            assistanceVulnerablePersons: $dto->assistanceVulnerablePersons,
            noticeAgreementVacatingPremises: $dto->noticeAgreementVacatingPremises,
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