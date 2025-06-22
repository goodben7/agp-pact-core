<?php

namespace App\State\GeneralParameter;

use ApiPlatform\State\ProcessorInterface;
use App\Manager\GenerateParameterManager;

class DeleteGeneralParameterProcessor implements ProcessorInterface
{
    public function __construct(private GenerateParameterManager $manager)
    {   
    }

    public function process(mixed $data, \ApiPlatform\Metadata\Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->manager->delete($uriVariables['id']);
    }

}