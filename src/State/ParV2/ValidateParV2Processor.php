<?php

namespace App\State\ParV2;

use ApiPlatform\State\ProcessorInterface;
use App\Dto\ParV2\ValidateParV2Dto;
use App\Manager\ParV2Manager;

class ValidateParV2Processor implements ProcessorInterface
{
    public function __construct(private ParV2Manager $manager)
    {   
    }

    /**
     * @param ValidateParV2Dto $data 
    */
    public function process(mixed $data, \ApiPlatform\Metadata\Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->manager->validate($data->par_v2);
    }
}
