<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function findBooksBySearch($search) 
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.title LIKE :search')
            ->orWhere('b.author LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getBooks(?int $offset = 0, int $limit = 5) {
        $qb = $this->createQueryBuilder('p');
        $qb->select()
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        return $qb->getQuery()->getArrayResult();
    }

    public function getCountBooks() {
        $qb = $this->createQueryBuilder('p');
        $qb->select(
            $qb->expr()->count('p.id')
        )
        ;

        return $qb->getQuery()->getSingleScalarResult();
    }

    // /**
    //  * @return Book[] Returns an array of Book objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
