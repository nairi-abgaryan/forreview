<?php

namespace App\Repository;

use App\Entity\TimeLang;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TimeLang|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeLang|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeLang[]    findAll()
 * @method TimeLang[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeLangRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TimeLang::class);
    }

//    /**
//     * @return TimeLang[] Returns an array of TimeLang objects
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
    public function findOneBySomeField($value): ?TimeLang
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
