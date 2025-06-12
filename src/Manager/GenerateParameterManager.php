<?php

namespace App\Manager;

use App\Dto\GeneralParameter\GeneralParameterCreateDTO;
use App\Entity\GeneralParameter;
use Doctrine\ORM\EntityManagerInterface;

readonly class GenerateParameterManager
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    public function create(GeneralParameterCreateDTO $data): GeneralParameter
    {
        $generalParameter = (new GeneralParameter())
            ->setCategory($data->category)
            ->setValue($data->value)
            ->setDescription($data->description)
            ->setActive($data->active)
            ->setCode($data->code)
            ->setDisplayOrder($data->displayOrder);

        $this->em->persist($generalParameter);
        $this->em->flush();

        return $generalParameter;
    }
}
