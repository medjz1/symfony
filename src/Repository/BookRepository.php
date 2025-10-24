<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Stmt\Return_;
use Symfony\Config\Framework\HttpClient\DefaultOptions\RetryFailedConfig;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    //    /**
    //     * @return Book[] Returns an array of Book objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Book
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function searchBookByRef($ref): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.ref=:ref')
            ->setParameter('ref', $ref)
            ->getQuery()
            ->getResult();
    }

    public function booksListByAuthors(): array
    {
        $query = $this->createQueryBuilder('b')
            ->join('b.author', 'a')
            ->addSelect('a')
            ->orderBy('a.username', 'ASC')
            ->getQuery();
        return $query->getResult();
    }

    public function booksBefore2023(): array
    {
        $query = $this->createQueryBuilder('b')
            ->join('b.author', 'a')
            ->where('b.publishDate< :date')
            ->andWhere('a.nb_books > :nb')
            ->setParameter('date', new \DateTime('2023-01-01'))
            ->setParameter('nb', 10)
            ->getQuery();
        return $query->getResult();
    }

    public function updateCategoryScience(): int
    {
        $query= $this->createQueryBuilder('b')
        ->update()
        ->set('b.category' ,':newCategory')
        ->where('b.category =:oldCategory')
        ->setParameter('newCategory', 'Romance')
        ->setParameter('oldCategory', 'Science-Fiction')
        ->getQuery();
        return $query->execute();
    }
}