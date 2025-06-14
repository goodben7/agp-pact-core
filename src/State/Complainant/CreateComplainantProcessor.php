<?php

namespace App\State\Complainant;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Complainant\ComplainantCreateDTO;
use App\Entity\Complainant;
use App\Manager\ComplainantManager;
use App\Model\NewComplainantModel;

readonly class CreateComplainantProcessor implements ProcessorInterface
{
    public function __construct(private ComplainantManager $manager)
    {
    }

    /** @var ComplainantCreateDTO $data */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Complainant
    {
       $model = new NewComplainantModel(
            $data->firstName,
            $data->lastName,
            $data->middleName,
            $data->contactPhone,
            $data->contactEmail,
            $data->plainPassword,
            $data->personType,
            $data->address,
            $data->province,
            $data->territory,
            $data->commune,
            $data->quartier,
            $data->city,
            $data->village,
            $data->secteur,
            $data->organizationStatus,
            $data->legalPersonality,
       );

       return $this->manager->create($model);

    }
}
