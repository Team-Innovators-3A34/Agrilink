<?php

namespace App\Repository;

use App\Entity\Ressources;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ressources>
 */
class RessourcesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ressources::class);
    }

    //    /**
    //     * @return Ressources[] Returns an array of Ressources objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Ressources
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
<<<<<<< HEAD
=======
    // Méthode pour filtrer les ressources par propriétaire (owner_id_id) et type
    public function findByOwnerAndType($ownerId = null, $type = null)
    {
        $qb = $this->createQueryBuilder('r');

        // Appliquer le filtre pour owner_id_id si fourni
        if ($ownerId) {
            $qb->andWhere('r.owner_id_id = :owner_id_id')
               ->setParameter('owner_id_id', $ownerId);
        }

        // Appliquer le filtre pour type si fourni
        if ($type) {
            $qb->andWhere('r.type = :type')
               ->setParameter('type', $type);
        }

        return $qb->getQuery()->getResult();
    }
>>>>>>> origin/gestionressources
}
