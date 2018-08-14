<?php

namespace App\Repository;

use App\Entity\PreferenceTagLang;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PreferenceTagLang|null find($id, $lockMode = null, $lockVersion = null)
 * @method PreferenceTagLang|null findOneBy(array $criteria, array $orderBy = null)
 * @method PreferenceTagLang[]    findAll()
 * @method PreferenceTagLang[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PreferenceTagLangRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PreferenceTagLang::class);
    }
}

