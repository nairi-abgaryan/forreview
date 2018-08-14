<?php

namespace App\Repository;

use App\Entity\VendorTypeLang;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method VendorTypeLang|null find($id, $lockMode = null, $lockVersion = null)
 * @method VendorTypeLang|null findOneBy(array $criteria, array $orderBy = null)
 * @method VendorTypeLang[]    findAll()
 * @method VendorTypeLang[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VendorTypeLangRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, VendorTypeLang::class);
    }
}

