<?php

namespace App\Manager;

use App\Dto\Import\CreateImportBatchDto;
use App\Entity\ImportBatch;
use App\Entity\User;
use App\Message\ProcessImportBatchMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class ImportBatchManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface    $bus,
        private Security               $security,
    )
    {
    }

    /**
     * @throws ExceptionInterface
     */
    public function create(CreateImportBatchDto $data): ImportBatch
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $batch = (new ImportBatch())
            ->setMapping($data->mapping)
            ->setEntityType($data->mapping->getEntityType())
            ->setUploadedBy($user);

        if ($data->file)
            $batch->setFile($data->file);


        $this->em->persist($batch);
        $this->em->flush();

        $this->bus->dispatch(new ProcessImportBatchMessage($batch->getId()));

        return $batch;
    }
}