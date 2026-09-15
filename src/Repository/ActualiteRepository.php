<?php

namespace App\Repository;

use App\Entity\Actualite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Actualite>
 */
class ActualiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Actualite::class);
    }

    // public function findLastFour(): array
    // {
    //     return $this->createQueryBuilder('a')
    //         ->where('a.dejapublier = true')
    //         ->orderBy('a.publierle', 'DESC')
    //         ->setMaxResults(4)
    //         ->getQuery()
    //         ->getResult();
    // }

    public function AfficherQuatreDernieresActualites(): array
    {
        return $this->createQueryBuilder('actualite')
                    ->where('actualite.dejapublier = true')
                    ->orderBy('actualite.publierle', 'DESC')
                    ->setMaxResults(4)
                    ->getQuery()
                    ->getResult();
    }

    public function rechercheActualite(?string $search,?string $publier): array
        {

            $qb = $this->createQueryBuilder('a');

            if($search){

                $qb->andWhere('a.titre LIKE :search
                                    OR a.contenu LIKE :search')

                ->setParameter( 'search','%' . $search . '%' );
            }

            if($publier !== null && $publier !== ''){

                $qb->andWhere('a.dejapublier = :publier')

                ->setParameter(  'publier', $publier);
            }

            $qb->orderBy('a.publierle', 'DESC');

            return $qb->getQuery()->getResult();
        }

        // Filtrage des actualités en fonction de la période (aujourd'hui, cette semaine, ce mois-ci)
       public function findFiltered(string $filter = 'all'): array
        {
            $qb = $this->createQueryBuilder('a')
                ->orderBy('a.publierle', 'DESC');

            $now = new \DateTime();

            switch ($filter) {

                case 'all':
                    $start = (clone $now)->modify('-50 days');

                    $qb->andWhere('a.publierle >= :start')
                        ->setParameter('start', $start);
                    break;

                case 'today':
                    $start = (clone $now)->setTime(0, 0);
                    $end = (clone $now)->setTime(23, 59, 59);

                    $qb->andWhere('a.publierle BETWEEN :start AND :end')
                        ->setParameter('start', $start)
                        ->setParameter('end', $end);
                    break;

                case 'week':
                    $start = (clone $now)->modify('monday this week')->setTime(0, 0);
                    $end = (clone $now)->modify('sunday this week')->setTime(23, 59, 59);

                    $qb->andWhere('a.publierle BETWEEN :start AND :end')
                        ->setParameter('start', $start)
                        ->setParameter('end', $end);
                    break;

                case 'month':
                    $start = (clone $now)->modify('first day of this month')->setTime(0, 0);
                    $end = (clone $now)->modify('last day of this month')->setTime(23, 59, 59);

                    $qb->andWhere('a.publierle BETWEEN :start AND :end')
                        ->setParameter('start', $start)
                        ->setParameter('end', $end);
                    break;
            }

            return $qb->getQuery()->getResult();
        }
  

//    /**
//     * @return Actualite[] Returns an array of Actualite objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Actualite
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
