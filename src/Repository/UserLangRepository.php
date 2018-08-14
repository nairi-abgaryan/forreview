<?php

namespace App\Repository;

use App\Entity\UserLang;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserLang|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserLang|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserLang[]    findAll()
 * @method UserLang[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserLangRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserLang::class);
    }

//    /**
//     * @return UserLang[] Returns an array of UserLang objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserLang
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
