<?php

namespace App\State\Complaint;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Complaint\ComplaintCreateDTO;
use App\Dto\Complaint\ComplaintUpdateDTO;
use App\Entity\Complaint;
use App\Manager\ComplaintManager;

readonly class UpdateComplaintProcessor implements ProcessorInterface
{
    public function __construct(private ComplaintManager $manager)
    {

    }

    /** @var ComplaintUpdateDTO $data */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Complaint
    {
        return $this->manager->update($data);
    }
}
