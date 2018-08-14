<?php

namespace App\Repository;

use App\Entity\TourLang;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TourLang|null find($id, $lockMode = null, $lockVersion = null)
 * @method TourLang|null findOneBy(array $criteria, array $orderBy = null)
 * @method TourLang[]    findAll()
 * @method TourLang[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TourLangRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TourLang::class);
    }

//    /**
//     * @return TourLang[] Returns an array of TourLang objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TourLang
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
