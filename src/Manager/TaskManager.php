<?php

namespace App\Manager;

use App\Entity\Task;
use App\Model\NewTaskModel;
use Doctrine\ORM\EntityManagerInterface;
use App\Message\Command\PendingTaskCommand;
use Symfony\Component\Messenger\MessageBusInterface;

class TaskManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface $bus,
    )
    {
    }
    
    public function create(NewTaskModel $model): Task
    {
        $task = new Task();

        $task->setType($model->type);
        $task->setMethod($model->method);
        $task->setMethod($model->method);
        $task->setData($model->data);
        $task->setData($model->data);
        $task->setCreatedBy($model->createdBy);
        $task->setCreatedAt($model->createdAt);
        $task->setExternalReferenceId($model->externalReferenceId);
        $task->setStatus(Task::STATUS_IDLE);
        $task->setData1($model->data1);
        $task->setData2($model->data2);
        $task->setData3($model->data3);
        $task->setData4($model->data4);
        $task->setData5($model->data5);
        $task->setData6($model->data6);

        $this->em->persist($task);
        $this->em->flush();
        
        $this->bus->dispatch(new PendingTaskCommand($task->getId()));

        return $task; 
    }
}