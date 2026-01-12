<?php

namespace App\Manager;

use App\Entity\Prejudice;
use App\Dto\PrejudiceCreateDTO;
use App\Dto\PrejudiceUpdateDTO;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UnavailableDataException;
use App\Exception\UnauthorizedActionException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

readonly class PrejudiceManager
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    public function create(PrejudiceCreateDTO $dto): Prejudice
    {
        $prejudice = (new Prejudice())
            ->setLabel($dto->label)
            ->setDescription($dto->description)
            ->setActive($dto->active ?? true)
            ->setAssetType($dto->assetType)
            ->setIsSensible($dto->isSensible)
            ->setDeleted(false);

        $this->em->persist($prejudice);
        $this->em->flush();

        return $prejudice;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
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

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function update(string $prejudiceId, PrejudiceUpdateDTO $dto): Prejudice
    {
        $prejudice = $this->findPrejudice($prejudiceId);

        ($prejudice)
            ->setLabel($dto->label ?? $prejudice->getLabel())
            ->setDescription($dto->description ?? $prejudice->getDescription())
            ->setAssetType($dto->assetType)
            ->setIsSensible($dto->isSensible)
            ->setActive($dto->active);

        $this->em->persist($prejudice);
        $this->em->flush();

        return $prejudice;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    private function findPrejudice(string $prejudiceId): Prejudice
    {
        $prejudice = $this->em->find(Prejudice::class, $prejudiceId);

        if (null === $prejudice)
            throw new UnavailableDataException(sprintf('cannot find Prejudice with id: %s', $prejudiceId));

        return $prejudice;
    }
}
