<?php

namespace App\MessageHandler;

use App\Entity\ImportBatch;
use App\Entity\ImportItem;
use App\Factory\ImporterStrategyFactory;
use App\Message\ProcessImportItemMessage;
use App\Repository\ImportItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class ProcessImportItemMessageHandler
{
    public function __construct(
        private ImportItemRepository    $itemRepository,
        private ImporterStrategyFactory $strategyFactory,
        private EntityManagerInterface  $em,
        private LoggerInterface         $logger,
    )
    {
    }

    public function __invoke(ProcessImportItemMessage $message): void
    {
        $item = $this->itemRepository->find($message->getItemId());
        if (!$item) {
            $this->logger->error('ImportItem not found for ID: ' . $message->getItemId());
            return;
        }

        $batch = $item->getBatch();

        try {
            $strategy = $this->strategyFactory->getStrategy($batch->getEntityType());
            $strategy->process($item);

            $item->setStatus(ImportItem::STATUS_SUCCESS);
            $batch->incrementSuccessfulItems();
        } catch (\Exception $e) {
            $item->setStatus(ImportItem::STATUS_FAILED);
            $item->setErrorMessage($e->getMessage());
            $batch->incrementFailedItems();
            $this->logger->warning('Failed to process import item ' . $item->getId(), ['exception' => $e]);
        }

        $item->setProcessedAt(new \DateTimeImmutable());
        $batch->incrementProcessedItems();

        if ($batch->isCompleted()) {
            $batch->setCompletedAt(new \DateTimeImmutable());
            $status = $batch->getFailedItems() > 0 ? ImportBatch::STATUS_PARTIAL_SUCCESS : ImportBatch::STATUS_COMPLETED;
            $batch->setStatus($status);
        }

        $this->em->flush();
    }
}