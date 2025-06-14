<?php

namespace App\State\Complaint;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Complaint\ApplyActionRequest;
use App\Entity\Complaint;
use App\Exception\UnavailableDataException;
use App\Manager\ComplaintWorkflowManager;
use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\WorkflowAction;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

readonly class ComplaintApplyActionProcessor implements ProcessorInterface
{
    public function __construct(
        private ComplaintWorkflowManager $manager,
        private EntityManagerInterface   $em
    )
    {

    }

    /**
     * @param ApplyActionRequest $data
     * @throws \Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Complaint
    {
        if (!$data instanceof ApplyActionRequest)
            throw new BadRequestHttpException('Invalid request data.');

        $complaint = $this->em->getRepository(Complaint::class)->findOneBy(['id' => $uriVariables['id']]);
        if (!$complaint)
            throw new UnavailableDataException('Complaint not found.');

        $action = $this->em->getRepository(WorkflowAction::class)->findOneBy(['id' => $data->actionId]);
        if (!$action)
            throw new UnavailableDataException(sprintf('Action with code "%s" not found.', $data->actionId));

        return $this->manager->applyAction($complaint, $action, $data->toArray());
    }
}
