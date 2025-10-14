<?php

namespace App\EventListener\Doctrine;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\Events;
use App\Message\Query\GetUserDetails;
use App\Message\Query\QueryBusInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;

#[AsDoctrineListener(event: Events::postPersist)]
class TaskEventListener
{

    public function __construct(
        private readonly Security $security,
        private readonly QueryBusInterface $queries,
        private readonly EntityManagerInterface $em
    )
    {
    }


    public function postPersist(LifecycleEventArgs $args)
    {
        $user = $this->security->getUser();
        if ($user === null) {
            return;
        }

        $userId = $this->security->getUser()->getUserIdentifier();

        /** @var User $user */
        $user = $this->queries->ask(new GetUserDetails(id: $userId));

        /** @var Task $task */
        $task = $args->getObject();


        if ($task instanceof Task) {
            $task->setSynchronizedBy($task->getCreatedBy() === 'SYSTEM' ? 'SYSTEM' : $user->getId());
            $task->setSynchronizedAt(new \DateTimeImmutable());
            $this->em->persist($task);
            $this->em->flush();
        }else{
            return null;
        }
    }
}