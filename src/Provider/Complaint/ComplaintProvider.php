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
        private ProviderInterface $itemProvider,
        private EntityManagerInterface $em
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // First, get the complaint object from the default item provider
        $result = $this->itemProvider->provide($operation, $uriVariables, $context);

        // If a complaint is found, and it's an instance of Complaint (for type safety)
        if ($result instanceof Complaint) {
            /** @var Complaint $complaint */
            $complaint = $result;

            // Ensure currentWorkflowStep is loaded if it's a proxy and not yet initialized
            // This might not be strictly necessary if Doctrine handles it, but adds robustness
            $currentWorkflowStep = $complaint->getCurrentWorkflowStep();

            if ($currentWorkflowStep) {
                // Find all workflow transitions where the 'fromStep' matches the current step of the complaint
                $transitions = $this->em->getRepository(WorkflowTransition::class)->findBy(['fromStep' => $currentWorkflowStep]);

                // Add the action of each found transition to the complaint's available actions
                foreach ($transitions as $transition) {
                    // This is where the error occurred. Ensure $availableActions is initialized.
                    // The constructor should handle this, but a defensive check can be useful.
                    $complaint->addAvailableAction($transition->getAction());
                }
            }
            return $complaint;
        }

        // If no complaint is found, or it's not a Complaint object, return null
        return $result;
    }
}
