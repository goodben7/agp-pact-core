<?php

namespace App\Manager;

use App\Entity\Prejudice;
use App\Dto\PrejudiceCreateDTO;
use App\Dto\PrejudiceUpdateDTO;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UnavailableDataException;
use App\Exception\UnauthorizedActionException;

class PrejudiceManager
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    public function create(PrejudiceCreateDTO $dto): Prejudice
    {
        $prejudice = new Prejudice();

        $prejudice->setLabel($dto->label);
        $prejudice->setCategory($dto->category);
        $prejudice->setComplaintType($dto->complaintType);
        $prejudice->setDescription($dto->description);
        $prejudice->setActive($dto->active ?? true);
        $prejudice->setIncidentCause($dto->incidentCause);
        $prejudice->setDeleted(false);

        foreach ($dto->consequences as $consequence) {
            $prejudice->addConsequence($consequence);
        }

        $this->em->persist($prejudice);
        $this->em->flush();

        return $prejudice;
    }

    public function delete(string $prejudiceId): void 
    {
        $prejudice = $this->findPrejudice($prejudiceId);

        if ($prejudice->isDeleted()) {
            throw new UnauthorizedActionException('this action is not allowed');
        }

        $prejudice->setDeleted(true);

        $this->em->persist($prejudice);
        $this->em->flush();
    }

    public function update(string $prejudiceId, PrejudiceUpdateDTO $dto): Prejudice
    {
        $prejudice = $this->findPrejudice($prejudiceId);

        $prejudice->setLabel($dto->label ?? $prejudice->getLabel());
        $prejudice->setCategory($dto->category  ?? $prejudice->getCategory());
        $prejudice->setComplaintType($dto->complaintType ?? $prejudice->getComplaintType());
        $prejudice->setDescription($dto->description ?? $prejudice->getDescription());
        $prejudice->setActive($dto->active ?? $prejudice->isActive());
        $prejudice->setIncidentCause($dto->incidentCause ?? $prejudice->getIncidentCause());

        foreach ($prejudice->getConsequences() as $existingConsequence) {
            $prejudice->removeConsequence($existingConsequence);
            $this->em->remove($existingConsequence);
        }

        foreach ($dto->consequences as $newConsequence) {
            $prejudice->addConsequence($newConsequence);
        }

        $this->em->persist($prejudice);
        $this->em->flush();

        return $prejudice;
    }

    private function findPrejudice(string $prejudiceId): Prejudice 
    {
        $prejudice = $this->em->find(Prejudice::class, $prejudiceId);

        if (null === $prejudice) {
            throw new UnavailableDataException(sprintf('cannot find Prejudice with id: %s', $prejudiceId));
        }

        return $prejudice; 
    }
}