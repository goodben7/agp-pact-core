<?php

namespace App\State;

use ApiPlatform\State\ProcessorInterface;
use App\Manager\PrejudiceManager;

class CreatePrejudiceProcessor implements ProcessorInterface
{
    public function __construct(private PrejudiceManager $manager)
    {   
    }

    /**
     * @param \App\Dto\PrejudiceCreateDTO $data 
    */
    public function process(mixed $data, \ApiPlatform\Metadata\Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->manager->create($data);
    }
}
