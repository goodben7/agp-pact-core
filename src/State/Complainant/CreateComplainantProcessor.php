<?php

namespace App\State\Complainant;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Complainant\ComplainantCreateDTO;
use App\Entity\Complainant;
use App\Manager\ComplainantManager;

readonly class CreateComplainantProcessor implements ProcessorInterface
{
    public function __construct(private ComplainantManager $manager)
    {
    }

    /** @var ComplainantCreateDTO $data */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Complainant
    {
        return $this->manager->create($data);
    }
}
