<?php

namespace App\State\Complaint;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Complaint\AssignCompanyRequest;
use App\Entity\Complaint;
use App\Manager\ComplaintManager;

readonly class ComplaintAssignCompanyProcessor implements ProcessorInterface
{
    public function __construct(
        private ComplaintManager $manager
    ) 
    {
    }

    /**
     * @param AssignCompanyRequest $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Complaint
    {
        return $this->manager->assignToCompany($uriVariables['id'], $data);
    }
}