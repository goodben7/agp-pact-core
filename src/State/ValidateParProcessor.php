<?php

namespace App\State;

use App\Manager\ParManager;
use ApiPlatform\State\ProcessorInterface;

class ValidateParProcessor implements ProcessorInterface
{
    public function __construct(private ParManager $manager)
    {   
    }

    /**
     * @param \App\Dto\ValidateParDto $data 
    */
    public function process(mixed $data, \ApiPlatform\Metadata\Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->manager->validate($data->par);
    }
}
