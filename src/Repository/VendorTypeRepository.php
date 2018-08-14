<?php

namespace App\Repository;

use App\Entity\VendorType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method vendorType|null find($id, $lockMode = null, $lockVersion = null)
 * @method vendorType|null findOneBy(array $criteria, array $orderBy = null)
 * @method vendorType[]    findAll()
 * @method vendorType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VendorTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, VendorType::class);
    }

    /**
     * @param $query
     * @param $inAppStatus
     * @return QueryBuilder
     */
    public function search($query, $inAppStatus)
    {
        $qb = $this->createQueryBuilder("vendor_type")
            ->leftJoin('vendor_type.vendorTypeLang', 'vendorTypeLang');

        if ($query) {
            $qb->orWhere('vendorTypeLang.title LIKE :query');
            $qb->orWhere('vendorTypeLang.description LIKE :query');
            $qb->setParameter('query', "%" . $query . "%");
        }

        $qb
            ->andWhere("vendor_type.appStatus = :appStatus")
            ->setParameter("appStatus", $inAppStatus)
            ->orderBy("vendor_type.createdAt", "DESC");

        return $qb->getQuery()->getResult();
    }
}
