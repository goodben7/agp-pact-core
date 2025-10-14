<?php

namespace App\State;

use App\Entity\Task;
use ApiPlatform\Metadata\Operation;
use App\Service\UploadedBase64File;
use App\Model\TaskFileManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use App\Message\Command\CheckPendingTasksCommand;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateTaskDocumentProcessor implements ProcessorInterface
{
    public const DIRECTORY = 'media';

    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface $bus,
        private TaskFileManagerInterface $manager,
        private UploadedBase64File $service)
    {
    }

    /**
     * @param \App\Dto\CreateTasktDocumentDto $data 
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
 
        $file = $this->service->handleBase64Image($data->file);
        $file = $this->manager->save($file, self::DIRECTORY);

        $task = new Task();

        $task->setType($data->type);
        $task->setMethod($data->method);
        $task->setMethod($data->method);
        $task->setData($data->data);
        $task->setCreatedBy($data->createdBy);
        $task->setCreatedAt($data->createdAt);
        $task->setExternalReferenceId($data->externalReferenceId);
        $task->setStatus(Task::STATUS_IDLE);
        $task->setData1($data->documentType); 
        $task->setData2($data->documentRefNumber);
        $task->setData3($data->agentId);
        $task->setData4($file);
        $task->setData5($data->data5); 
        $task->setData6($data->data6);

        $this->em->persist($task);
        $this->em->flush();

        $this->bus->dispatch(new CheckPendingTasksCommand());
        
        return $task;
    }
}