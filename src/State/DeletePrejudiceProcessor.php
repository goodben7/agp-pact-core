<?php

namespace App\State;

use ApiPlatform\State\ProcessorInterface;
use App\Manager\PrejudiceManager;

class DeletePrejudiceProcessor implements ProcessorInterface
{
    public function __construct(private PrejudiceManager $manager)
    {   
    }

    public function process(mixed $data, \ApiPlatform\Metadata\Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->manager->delete($uriVariables['id']);
    }
}
