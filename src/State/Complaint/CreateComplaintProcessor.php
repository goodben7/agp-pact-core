<?php

namespace App\State\Complaint;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Complaint\ComplaintCreateDTO;
use App\Entity\Complaint;
use App\Manager\ComplaintManager;

readonly class CreateComplaintProcessor implements ProcessorInterface
{
    public function __construct(private ComplaintManager $manager)
    {

    }

    /** @var ComplaintCreateDTO $data */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Complaint
    {
        return $this->manager->create($data);
    }
}
