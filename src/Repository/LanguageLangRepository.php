<?php

namespace App\Repository;

use App\Entity\LanguageLang;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LanguageLang|null find($id, $lockMode = null, $lockVersion = null)
 * @method LanguageLang|null findOneBy(array $criteria, array $orderBy = null)
 * @method LanguageLang[]    findAll()
 * @method LanguageLang[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LanguageLangRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LanguageLang::class);
    }
}
