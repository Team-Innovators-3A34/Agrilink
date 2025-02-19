<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }


    public function searchEvents(?string $nom, ?\DateTimeInterface $date, ?string $adresse)
{
    $qb = $this->createQueryBuilder('e');

    if ($nom) {
        $qb->andWhere('e.nom LIKE :nom')
           ->setParameter('nom', '%' . $nom . '%');
    }

    if ($adresse) {
        $qb->andWhere('e.adresse LIKE :adresse')
           ->setParameter('adresse', '%' . $adresse . '%');
    }

    if ($date) {
        $qb->andWhere('e.date = :date')
           ->setParameter('date', $date->format('Y-m-d'));
    }

    return $qb->getQuery()->getResult();
}



public function findByFilters(?string $nom, ?\DateTimeInterface $date, ?string $adresse, ?string $sortField, ?string $sortDirection)
{
    $qb = $this->createQueryBuilder('e');

    if ($nom) {
        $qb->andWhere('e.nom LIKE :nom')
           ->setParameter('nom', '%' . $nom . '%');
    }

    if ($date) {
        $qb->andWhere('e.date = :date')
           ->setParameter('date', $date->format('Y-m-d'));
    }

    if ($adresse) {
        $qb->andWhere('e.adresse LIKE :adresse')
           ->setParameter('adresse', '%' . $adresse . '%');
    }

    // Handle Sorting
    $allowedSortFields = ['nom', 'date', 'nbr_places'];
    if ($sortField && in_array($sortField, $allowedSortFields)) {
        $sortDirection = ($sortDirection === 'desc') ? 'DESC' : 'ASC';
        $qb->orderBy('e.' . $sortField, $sortDirection);
    }

    return $qb->getQuery()->getResult();
}


    //    /**
    //     * @return Event[] Returns an array of Event objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Event
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
