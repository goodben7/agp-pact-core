<?php

namespace App\Repository;

use App\Entity\Prejudice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Prejudice>
 */
class PrejudiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prejudice::class);
    }

    /**
     * Find prejudices by complaint type code
     * 
     * @param string $complaintTypeCode
     * @return Prejudice[] Returns an array of Prejudice objects
     */
    public function findByComplaintTypeCode(string $complaintTypeCode): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.complaintType', 'ct')
            ->andWhere('ct.code = :code')
            ->setParameter('code', $complaintTypeCode)
            ->getQuery()
            ->getResult()
        ;
    }

    //    /**
    //     * @return Prejudice[] Returns an array of Prejudice objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Prejudice
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
