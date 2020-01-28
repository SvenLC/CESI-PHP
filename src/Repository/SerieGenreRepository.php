<?php

namespace App\Repository;

use App\Entity\SerieGenre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SerieGenre|null find($id, $lockMode = null, $lockVersion = null)
 * @method SerieGenre|null findOneBy(array $criteria, array $orderBy = null)
 * @method SerieGenre[]    findAll()
 * @method SerieGenre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SerieGenreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SerieGenre::class);
    }

    // /**
    //  * @return SerieGenre[] Returns an array of SerieGenre objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SerieGenre
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
