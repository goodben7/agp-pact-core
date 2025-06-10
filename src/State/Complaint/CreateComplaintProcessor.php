<?php

namespace App\State\Complaint;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\CreateUserDto;
use App\Entity\Complaint;
use App\Manager\ComplaintManager;
use App\Model\NewUserModel;

readonly class CreateComplaintProcessor implements ProcessorInterface
{
    public function __construct(private ComplaintManager $manager)
    {

    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Complaint
    {
        return $this->manager->create();
    }
}
