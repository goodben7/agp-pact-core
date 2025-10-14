<?php

namespace App\Service;

use App\Entity\Task;
use Psr\Log\LoggerInterface;
use App\Repository\TaskRepository;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class TaskRunner
{
    /** @var array<\App\Model\TaskRunnerInterface> */
    private $runners;

    public function __construct(
        #[TaggedIterator('sync.task_runner')] iterable $runners,
        private readonly TaskRepository $repository,
        private readonly LoggerInterface $logger
    )
    {
        $this->runners = iterator_to_array($runners);
    }

    public function run(Task $task): void
    {
        try {
            if ($task->getStatus() !== Task::STATUS_IDLE) {
                $this->logger->warning("cannot run task {$task->getId()} because it is already running.");
                return;
            }

            $this->repository->updateTaskStatus($task->getId(), Task::STATUS_INPROGRESS, null);

            $found = false; 

            \reset($this->runners);

            /** @var \App\Model\TaskRunnerInterface $runner */
            while (null != $runner = \current($this->runners)) {
                if ($runner->support($task->getType())) {
                    $found = true; 
                    $runner->run($task);
                    break;
                }

                \next($this->runners);
            }
            if (!$found){
                $this->logger->warning("no runner found to handle task {$task->getId()} of type {$task->getType()}");
        
                $this->repository->updateTaskStatus($task->getId(), Task::STATUS_FAILED, "invalid Task, no runner available");
            }

        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
        } finally {
            if (!$this->repository->isTaskTerminated($task->getId()))
                $this->repository->updateTaskStatus($task->getId(), Task::STATUS_IDLE, null);
        }
    }
}