<?php

namespace App\Manager;

use App\Dto\Import\CreateImportMappingDto;
use App\Entity\ImportMapping;
use App\Service\ActivityLogDispatcher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

readonly class ImportMappingManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private NormalizerInterface $normalizer,
        private ActivityLogDispatcher  $activityLogDispatcher
    )
    {
    }

    /**
     * @throws ExceptionInterface
     */
    public function create(CreateImportMappingDto $dto): ImportMapping
    {
        $mappingConfiguration = $this->normalizer->normalize($dto->mappingConfiguration);

        $importMapping = (new ImportMapping())
            ->setName($dto->name)
            ->setEntityType($dto->entityType)
            ->setMappingConfiguration($mappingConfiguration);

        $this->em->persist($importMapping);
        $this->em->flush();

        $this->activityLogDispatcher->dispatch('create', $importMapping);

        return $importMapping;
    }
}