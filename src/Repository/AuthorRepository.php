<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    //    /**
    //     * @return Author[] Returns an array of Author objects
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

    //    public function findOneBySomeField($value): ?Author
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


    // select By name DQL

    // utilise the EntityManager
    // utilise la fonction createQuery
    // crée une requette qui ressemble au SQL
       public function findByName($value): array
       {
           $query=$this->getEntityManager()
           ->createQuery('Select a From App\Entity\Author a WHERE a.username=:name')
           ->setParameter('name',$value);
           return $query->getResult();
       }

        public function findByMinMax($min, $max): array
       {
           $query=$this->getEntityManager()
           ->createQuery('Select a From App\Entity\Author a WHERE a.nb_book BETWEEN :min AND :max')
           ->setParameter('min',$min)
           ->setParameter('max',$max);
           return $query->getResult();
       }
}
