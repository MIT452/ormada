<?php

namespace App\Repository;

use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evenement>
 */
class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }

    public function AfficherQuatreDerniersEvenements(): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.datedudebut', 'DESC') // ou date événement
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }

     public function AfficherTroisDerniersEvenements(): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.datedudebut', 'DESC') // ou date événement
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

// Recherche d'événements avec des critères de recherche
    public function searchEvenements(
        ?string $search,
        ?string $dateDebut,
        ?string $dateFin
    ): array
    {
        $qb = $this->createQueryBuilder('e');

        if ($search) {

            $qb->andWhere('
                e.titre LIKE :search
                OR e.description LIKE :search
            ')
            ->setParameter(
                'search',
                '%' . $search . '%'
            );
        }

        if ($dateDebut) {

            $qb->andWhere(
                'e.datedudebut >= :dateDebut'
            )
            ->setParameter(
                'dateDebut',
                $dateDebut
            );
        }

        if ($dateFin) {

            $qb->andWhere(
                'e.datedufin <= :dateFin'
            )
            ->setParameter(
                'dateFin',
                $dateFin
            );
        }

    
        $qb->orderBy('e.datedudebut', 'ASC');

        return $qb->getQuery()->getResult();
    }


    // Filtrage des événements en fonction de leur statut (passé, en cours, à venir)
    public function findFiltered(string $filter): array
    {
        $qb = $this->createQueryBuilder('e');
        $now = new \DateTimeImmutable();

        if ($filter === 'past') {
            $qb->andWhere('e.datedufin < :now')
            ->setParameter('now', $now);
        }

        if ($filter === 'current') {
            $qb->andWhere('e.datedudebut <= :now')
            ->andWhere('e.datedufin >= :now')
            ->setParameter('now', $now);
        }

        if ($filter === 'upcoming') {
            $qb->andWhere('e.datedudebut > :now')
            ->setParameter('now', $now);
        }

        return $qb->orderBy('e.datedudebut', 'ASC')
                ->getQuery()
                ->getResult();
    }


//    /**
//     * @return Evenement[] Returns an array of Evenement objects
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

//    public function findOneBySomeField($value): ?Evenement
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
