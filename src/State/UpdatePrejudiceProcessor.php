<?php

namespace App\State;

use ApiPlatform\State\ProcessorInterface;
use App\Manager\PrejudiceManager;

class UpdatePrejudiceProcessor implements ProcessorInterface
{
    public function __construct(private PrejudiceManager $manager)
    {   
    }

    /**
     * @param \App\Dto\PrejudiceUpdateDTO $data 
    */
    public function process(mixed $data, \ApiPlatform\Metadata\Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->manager->update($uriVariables['id'], $data);
    }
}
