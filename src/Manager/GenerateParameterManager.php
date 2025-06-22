<?php

namespace App\Manager;

use App\Entity\GeneralParameter;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UnavailableDataException;
use App\Exception\UnauthorizedActionException;
use App\Dto\GeneralParameter\GeneralParameterCreateDTO;

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

    public function delete(string $generalParameterId): void 
    {
        $generalParameter = $this->findGeneralParameter($generalParameterId);

        if ($generalParameter->isDeleted()) {
            throw new UnauthorizedActionException('this action is not allowed');
        }

        $generalParameter->setDeleted(true);

        $this->em->persist($generalParameter);
        $this->em->flush();
    }

    private function findGeneralParameter(string $generalParameterId): GeneralParameter 
    {
        $generalParameter = $this->em->find(GeneralParameter::class, $generalParameterId);

        if (null === $generalParameter) {
            throw new UnavailableDataException(sprintf('cannot find general Parameter with id: %s', $generalParameterId));
        }

        return $generalParameter; 
    }
}
