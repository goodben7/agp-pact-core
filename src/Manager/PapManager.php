<?php

namespace App\Manager;

use App\Entity\Pap;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UnavailableDataException;
use App\Exception\UnauthorizedActionException;

class PapManager
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    private function findPap(string $PapId): Pap 
    {
        $pap = $this->em->find(Pap::class, $PapId);

        if (null === $pap) {
            throw new UnavailableDataException(sprintf('cannot find pap with id: %s', $PapId));
        }

        return $pap; 
    }

    /**
     * Summary of delete
     * @param string $PapId
     * @param string $reason
     * @throws \App\Exception\UnauthorizedActionException
     * @return void
     */
    public function delete(string $PapId, string $reason): void 
    {
        $pap = $this->findPap($PapId);

        if ($pap->getDeleted()) {
            throw new UnauthorizedActionException('this action is not allowed');
        }

        $pap->setDeleted(true);
        $pap->setReasonDeletion($reason);

        $this->em->persist($pap);
        $this->em->flush();
    }

}