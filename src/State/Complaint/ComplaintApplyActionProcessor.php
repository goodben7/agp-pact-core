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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

readonly class ComplaintApplyActionProcessor implements ProcessorInterface
{
    public function __construct(
        private ComplaintWorkflowManager $manager,
        private EntityManagerInterface   $em,
        private RequestStack             $requestStack
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

        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            throw new \RuntimeException('No current request found.');
        }

        $data->setFromArray($request->request->all() + $request->files->all());

        $complaint = $this->em->getRepository(Complaint::class)->findOneBy(['id' => $uriVariables['id']]);
        if (!$complaint)
            throw new UnavailableDataException('Complaint not found.');

        $action = $this->em->getRepository(WorkflowAction::class)->findOneBy(['id' => $data->actionId]);
        if (!$action)
            throw new UnavailableDataException(sprintf('Action with code "%s" not found.', $data->actionId));

        return $this->manager->applyAction($complaint, $action, $data->toArray());
    }
}
