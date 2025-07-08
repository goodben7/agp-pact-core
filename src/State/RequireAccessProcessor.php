<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Manager\MemberManager;

class RequireAccessProcessor implements ProcessorInterface
{
    public function __construct(
        private MemberManager $manager
    )
    {
    }

    /**
     * @param \App\Dto\RequireAccessDto $data 
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->manager->setAccess($data, $uriVariables['id']);
    }
}