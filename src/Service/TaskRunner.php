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
        
        // Log all registered runners and their supported types
        $this->logger->info("TaskRunner initialized with " . count($this->runners) . " runners");
        foreach ($this->runners as $runner) {
            $runnerClass = get_class($runner);
            $this->logger->debug("Registered runner: {$runnerClass}");
        }
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
            
            $this->logger->info("Looking for a runner to handle task {$task->getId()} of type {$task->getType()}");
            $this->logger->debug("Available runners: " . count($this->runners));

            /** @var \App\Model\TaskRunnerInterface $runner */
            while (null != $runner = \current($this->runners)) {
                $runnerClass = get_class($runner);
                $this->logger->debug("Checking if runner {$runnerClass} supports task type {$task->getType()}");
                
                $taskType = $task->getType();
                $this->logger->debug("Comparing task type '{$taskType}' with runner supported type");
                
                // Get the supported type from the runner if it has a SUPPORT_TYPE constant
                $supportedType = null;
                $reflection = new \ReflectionClass($runner);
                if ($reflection->hasConstant('SUPPORT_TYPE')) {
                    $supportedType = $reflection->getConstant('SUPPORT_TYPE');
                    $this->logger->debug("Runner {$runnerClass} has SUPPORT_TYPE constant: '{$supportedType}'");
                }
                
                if ($runner->support($taskType)) {
                    $this->logger->info("Found compatible runner {$runnerClass} for task {$task->getId()} of type {$taskType}");
                    $found = true; 
                    $runner->run($task);
                    break;
                } else {
                    $this->logger->debug("Runner {$runnerClass} does not support task type '{$taskType}'");
                    if ($supportedType !== null) {
                        $this->logger->debug("Case-sensitive comparison: task type '{$taskType}' === '{$supportedType}' is " . ($taskType === $supportedType ? 'true' : 'false'));
                        $this->logger->debug("Case-insensitive comparison: task type '{$taskType}' === '{$supportedType}' is " . (strtoupper($taskType) === strtoupper($supportedType) ? 'true' : 'false'));
                    }
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