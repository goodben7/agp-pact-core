<?php

namespace App\Repository;

use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Location>
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    /**
     * Trouve toutes les localisations descendantes d'un ancêtre donné,
     * qui correspondent à un niveau spécifique.
     *
     * @param string $ancestorId L'ID de la localisation de départ (ex: une province).
     * @param string $levelCode Le code du niveau des descendants à trouver (ex: 'VILLAGE').
     * @return Location[]
     */
    public function findDescendantsByLevel(string $ancestorId, string $levelCode): array
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Location::class, 'l');

        $sql = <<<SQL
        WITH RECURSIVE location_descendants (id, parent_id, path) AS (
            SELECT
                id,
                parent_id,
                CAST(CONVERT(id USING utf8mb4) COLLATE utf8mb4_unicode_ci AS CHAR(1000)) AS path
            FROM location
            WHERE id = :ancestorId

            UNION ALL

            SELECT
                l.id,
                l.parent_id,
                CAST(CONVERT(CONCAT(ld.path, '->', l.id) USING utf8mb4) COLLATE utf8mb4_unicode_ci AS CHAR(1000))
            FROM location l
            INNER JOIN location_descendants ld ON l.parent_id = ld.id
            WHERE FIND_IN_SET(
                CONVERT(l.id USING utf8mb4) COLLATE utf8mb4_unicode_ci,
                REPLACE(ld.path, '->', ',')
            ) = 0
        )
        SELECT l.*
        FROM location l
        INNER JOIN location_descendants ld ON l.id = ld.id
        INNER JOIN general_parameter gp ON l.level_id = gp.id
        WHERE gp.code = :levelCode
          AND l.id != :ancestorId
        SQL;

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('ancestorId', $ancestorId);
        $query->setParameter('levelCode', $levelCode);

        return $query->getResult();
    }
}
