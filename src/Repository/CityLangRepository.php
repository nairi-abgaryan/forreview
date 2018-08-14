<?php

namespace App\Repository;

use App\Entity\CityLang;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CityLang|null find($id, $lockMode = null, $lockVersion = null)
 * @method CityLang|null findOneBy(array $criteria, array $orderBy = null)
 * @method CityLang[]    findAll()
 * @method CityLang[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityLangRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CityLang::class);
    }

//    /**
//     * @return CityLang[] Returns an array of CityLang objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CityLang
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
