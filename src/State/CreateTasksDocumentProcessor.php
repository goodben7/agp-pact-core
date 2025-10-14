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

class CreateTasksDocumentProcessor implements ProcessorInterface
{
    const DIRECTORY = 'media';

    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface $bus,
        private TaskFileManagerInterface $manager,
        private UploadedBase64File $service)
    {
    }

    /**
     * @param \App\Dto\CreateTaskstDocumentDto $data 
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        foreach($data->tasks as $value)
        {
            $file = $this->service->handleBase64Image($value->file);
            $file = $this->manager->save($file, self::DIRECTORY);

            $task = new Task();

            $task->setType($value->type);
            $task->setMethod($value->method);   
            $task->setData($value->data);   
            $task->setCreatedBy($value->createdBy);
            $task->setCreatedAt($value->createdAt);
            $task->setExternalReferenceId($value->externalReferenceId);
            $task->setStatus(Task::STATUS_IDLE);
            $task->setData1($value->documentType);
            $task->setData2($value->documentRefNumber);
            $task->setData3($value->agentId);
            $task->setData4($file);
            $task->setData5($value->data5);
            $task->setData6($value->data6);

            $this->em->persist($task);
        }

        $this->em->flush();

        $this->bus->dispatch(new CheckPendingTasksCommand());
        
        return $task;
    }
}