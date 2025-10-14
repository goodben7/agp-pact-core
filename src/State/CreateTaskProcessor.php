<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Manager\TaskManager;
use App\Model\NewTaskModel;

class CreateTaskProcessor implements ProcessorInterface
{

    public function __construct(private TaskManager $manager)
    {
        
    }

    /**
     * @param \App\Dto\CreateTasktDto $data 
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $model = new NewTaskModel();
       
        $model->type = $data->type;
        $model->method = $data->method;
        $model->data = $data->data;
        $model->createdBy = $data->createdBy;
        $model->externalReferenceId = $data->externalReferenceId;
        $model->createdAt = $data->createdAt;
        $model->data1 = $data->data1;
        $model->data2 = $data->data2;
        $model->data3 = $data->data3;
        $model->data4 = $data->data4;
        $model->data5 = $data->data5;
        $model->data6 = $data->data6;

        return $this->manager->create($model);
    }
}