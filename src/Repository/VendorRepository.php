<?php

namespace App\Repository;

use App\Entity\Vendor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

class VendorRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Vendor::class);
    }

    /**
     * @param $query
     * @param $status
     * @param $inAppStatus
     * @param $activity
     * @param $vendorType
     * @param $country
     * @param $city
     * @return QueryBuilder
     */
    public function search($query, $status, $inAppStatus, $activity, $vendorType, $country, $city)
    {
        $qb = $this->createQueryBuilder("vendor")
            ->leftJoin('vendor.vendorLang', 'vendorLang')
        ;

        if ($query) {
            $qb->orWhere('vendorLang.title LIKE :query');
            $qb->orWhere('vendorLang.description LIKE :query');
            $qb->setParameter('query', "%" . $query . "%");
        }

        if ($activity) {
            $qb->leftJoin('vendor.activity', 'activity');
            $qb->andWhere('activity = :activity');
            $qb->setParameter('activity', $activity );
        }

        if ($vendorType) {
            $qb->leftJoin('vendor.vendorType', 'vendorType');
            $qb->andWhere('vendorType = :vendorType');
            $qb->setParameter('vendorType', $vendorType );
        }

        if ($country){
            $qb->andWhere('vendor.country = :country');
            $qb->setParameter('country', $country );
        }

        if ($city){
            $qb->andWhere("vendor.city IN ($city)");
        }

        $qb
            ->andWhere("vendor.appStatus = :appStatus")
            ->setParameter("appStatus", $inAppStatus)
            ->andWhere("vendor.status = :status")
            ->setParameter("status", $status)
            ->orderBy("vendor.createdAt","DESC");

        return $qb;
    }
}
