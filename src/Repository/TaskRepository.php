<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $em)
    {
        parent::__construct($registry, Task::class);
    }

    public function updateTaskStatus(string $id, string $status, ?string $message): void
    {
        $query = $this->em->createQuery(sprintf('UPDATE %s t SET t.status = :status, t.message = :message WHERE t.id = :id', Task::class));
        $query->setParameter('id', $id);
        $query->setParameter('status', $status);
        $query->setParameter('message', $message);
        $query->execute();
    }

    public function isTaskTerminated(string $id): bool
    {
        $query = $this->em->createQuery(
            'SELECT CASE 
                WHEN t.status IN (:terminatedStatuses) THEN true
                ELSE false
            END
            FROM App\Entity\Task t
            WHERE t.id = :id'
        );
        $query->setParameter('id', $id);
        $query->setParameter('terminatedStatuses', [
            Task::STATUS_TERMINATED,
            Task::STATUS_FAILED
        ]);
    
        return (bool) $query->getSingleScalarResult();
    }
    
    public function findOneById(string $id): ?Task
    {
        return $this->em
            ->createQueryBuilder()
            ->select('t')
            ->from(Task::class, 't')
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult() 
        ; 
    }

    /**
     * @return Task[] 
     */
    public function getPendingTasks(): array
    {
        return $this->createQueryBuilder('t')
            ->Where('t.status = :status')
            ->setParameter('status', Task::STATUS_IDLE)
            ->getQuery()
            ->getResult()
        ;
    }
    
    //    /**
    //     * @return Task[] Returns an array of Task objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Task
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
