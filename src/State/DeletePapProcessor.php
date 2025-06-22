<?php

namespace App\State;

use ApiPlatform\State\ProcessorInterface;
use App\Manager\PapManager;

class DeletePapProcessor implements ProcessorInterface
{
    public function __construct(private PapManager $manager)
    {   
    }

    /**
     * @param \App\Dto\DeletePapDto $data 
     */
    public function process(mixed $data, \ApiPlatform\Metadata\Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->manager->delete($uriVariables['id'], $data->reason);
    }

}