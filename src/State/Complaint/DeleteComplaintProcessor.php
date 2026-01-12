<?php

namespace App\State\Complaint;

use App\Entity\Complaint;
use App\Entity\User;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class DeleteComplaintProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ) 
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if (!$data instanceof Complaint) {
            throw new NotFoundHttpException('Complaint not found.');
        }

        /** @var User|null $user */
        $user = $this->security->getUser();

        // Soft delete: set deleted flag and metadata
        $data->setDeleted(true);
        $data->setDeletedAt(new \DateTimeImmutable());
        $data->setDeletedBy($user?->getId());

        $this->em->flush();
    }
}
