<?php

namespace App\MessageHandler;

use App\Entity\ImportBatch;
use App\Entity\ImportItem;
use App\Exception\UnavailableDataException;
use App\Message\ProcessImportItemMessage;
use App\Repository\ImportBatchRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\ProcessImportBatchMessage;

#[AsMessageHandler]
readonly class ProcessImportBatchMessageHandler
{
    public function __construct(
        private ImportBatchRepository  $batchRepository,
        private EntityManagerInterface $em,
        private MessageBusInterface    $bus,
        private LoggerInterface        $logger,
        private string                 $importUploadsDirectory
    )
    {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(ProcessImportBatchMessage $message): void
    {
        $batch = $this->batchRepository->find($message->getBatchId());

        if (!$batch) {
            $this->logger->error('ImportBatch not found for ID: ' . $message->getBatchId());
            return;
        }

        if ($batch->getStatus() !== ImportBatch::STATUS_PENDING) {
            $this->logger->warning(
                sprintf("Attempted to process batch %s with status %s, expected %s.", $batch->getId(), $batch->getStatus(), ImportBatch::STATUS_PENDING)
            );
            return;
        }

        $batch->setStatus(ImportBatch::STATUS_PROCESSING);
        $this->em->flush();

        try {
            $filePath = $this->importUploadsDirectory . '/' . $batch->getFilePath();
            if (!file_exists($filePath)) {
                throw new UnavailableDataException('File not found: ' . $filePath);
            }

            $csv = Reader::createFromPath($filePath, 'r');
            $csv->setHeaderOffset(0);

            $records = $csv->getRecords();
            $totalItems = iterator_count($csv->getRecords());
            $batch->setTotalItems($totalItems);

            $itemsToDispatch = [];
            foreach ($records as $record) {
                $item = (new ImportItem())
                    ->setBatch($batch)
                    ->setRowData($record);

                $this->em->persist($item);
                $itemsToDispatch[] = $item;
            }

            if (!empty($itemsToDispatch)) {
                $this->em->flush();
                foreach ($itemsToDispatch as $item) {
                    $this->bus->dispatch(new ProcessImportItemMessage($item->getId()));
                }
            }
        } catch (\Exception $e) {
            $batch->setStatus(ImportBatch::STATUS_FAILED);
            $this->logger->critical('Failed to process import batch ' . $batch->getId(), ['exception' => $e]);

            $this->em->flush();
        }
    }
}
