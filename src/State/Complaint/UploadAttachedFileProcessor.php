<?php

namespace App\State\Complaint;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Complaint\AttachedFileDto;
use App\Entity\Complaint;
use App\Exception\UnavailableDataException;
use App\Manager\ComplaintManager;
use Doctrine\ORM\EntityManagerInterface;

readonly class UploadAttachedFileProcessor implements ProcessorInterface
{
    public function __construct(
        private ComplaintManager $manager,
        private EntityManagerInterface $em
    ) {
    }

    /**
     * @param AttachedFileDto $data
     * @throws \Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Complaint
    {
        $complaint = $this->em->getRepository(Complaint::class)->findOneBy([
            'id' => $data->complaintId,
            'deleted' => false
        ]);
        if (!$complaint) {
            throw new UnavailableDataException('Complaint not found.');
        }

        return $this->manager->uploadFiles($complaint, $data);
    }
}
