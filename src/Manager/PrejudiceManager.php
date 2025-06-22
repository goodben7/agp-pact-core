<?php

namespace App\Manager;

use App\Entity\Prejudice;
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

    private function findPrejudice(string $prejudiceId): Prejudice 
    {
        $prejudice = $this->em->find(Prejudice::class, $prejudiceId);

        if (null === $prejudice) {
            throw new UnavailableDataException(sprintf('cannot find Prejudice with id: %s', $prejudiceId));
        }

        return $prejudice; 
    }
}