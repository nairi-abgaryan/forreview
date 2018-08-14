<?php

namespace App\Repository;

use App\Entity\ActivityLang;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ActivityLang|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivityLang|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActivityLang[]    findAll()
 * @method ActivityLang[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityLangRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ActivityLang::class);
    }
}
