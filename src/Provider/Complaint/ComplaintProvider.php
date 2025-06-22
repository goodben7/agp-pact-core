<?php

namespace App\Provider\Complaint;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Complaint;
use App\Entity\WorkflowTransition;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class ComplaintProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface      $itemProvider,
        private EntityManagerInterface $em
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $result = $this->itemProvider->provide($operation, $uriVariables, $context);

        if ($result instanceof Complaint) {
            /** @var Complaint $complaint */
            $complaint = $result;

            $currentWorkflowStep = $complaint->getCurrentWorkflowStep();
            if ($currentWorkflowStep) {
                $transitions = $this->em->getRepository(WorkflowTransition::class)->findBy(['fromStep' => $currentWorkflowStep]);
                foreach ($transitions as $transition)
                    $complaint->addAvailableAction($transition->getAction());
            }
            return $complaint;
        }

        return $result;
    }
}
