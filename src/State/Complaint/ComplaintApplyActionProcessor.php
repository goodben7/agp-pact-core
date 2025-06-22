<?php

namespace App\State\Complaint;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Complaint\ApplyActionRequest;
use App\Entity\Complaint;
use App\Entity\WorkflowAction;
use App\Exception\UnavailableDataException;
use App\Manager\ComplaintWorkflowManager;
use Doctrine\ORM\EntityManagerInterface;
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
        if (is_array($data)) {
            $requestDto = new ApplyActionRequest();
            $requestDto->setFromArray($data);
        } elseif ($data instanceof ApplyActionRequest) {
            $requestDto = $data;
        } else {
            throw new BadRequestHttpException('Invalid request data type: ' . gettype($data));
        }

        if (!$requestDto->actionId) {
            throw new BadRequestHttpException('actionId is required.');
        }

        $complaint = $this->em->getRepository(Complaint::class)->find($uriVariables['id']);
        if (!$complaint) {
            throw new UnavailableDataException('Complaint not found.');
        }

        $action = $this->em->getRepository(WorkflowAction::class)->find($requestDto->actionId);
        if (!$action) {
            throw new UnavailableDataException(sprintf('Action with ID "%s" not found.', $requestDto->actionId));
        }

        return $this->manager->applyAction($complaint, $action, $data->toArray());
    }
}
