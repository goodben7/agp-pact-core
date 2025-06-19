<?php

namespace App\State;

use App\Manager\UserManager;
use ApiPlatform\Metadata\Operation;
use App\Model\NewRegisterUserModel;
use ApiPlatform\State\ProcessorInterface;

class RegisterUserProcessor implements ProcessorInterface
{
    public function __construct(private UserManager $manager)
    {
        
    }

    /**
     * @param \App\Dto\NewRegisterUserDto $data 
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        $model = new NewRegisterUserModel(
            $data->email, 
            $data->plainPassword, 
            $data->phone, 
            $data->displayName
        );

        return $this->manager->register($model);
    }
}
