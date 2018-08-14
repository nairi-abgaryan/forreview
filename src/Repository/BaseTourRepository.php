<?php

namespace App\Repository;

use App\Entity\BaseTour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BaseTour|null find($id, $lockMode = null, $lockVersion = null)
 * @method BaseTour|null findOneBy(array $criteria, array $orderBy = null)
 * @method BaseTour[]    findAll()
 * @method BaseTour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BaseTourRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BaseTour::class);
    }

    /**
     * @param $query
     * @param $inAppStatus
     * @param $activity
     * @param $city
     * @param $country
     * @return QueryBuilder
     */
    public function search($query, $inAppStatus, $activity, $city, $country)
    {
        $qb = $this->createQueryBuilder("base_tour")
            ->leftJoin('base_tour.baseTourLang', 'baseTourLang');

        if ($query) {
            $qb->leftJoin('base_tour.country', 'country');
            $qb->leftJoin('country.countryLang', 'countryLang');
            $qb->orWhere('countryLang.name LIKE :query');
            $qb->orWhere('baseTourLang.title LIKE :query');
            $qb->orWhere('baseTourLang.description LIKE :query');
            $qb->setParameter('query', "%" . $query . "%");
        }

        if ($activity) {
            $qb->leftJoin('base_tour.activity', 'activity');
            $qb->andWhere('activity = :activity');
            $qb->setParameter('activity', $activity );
        }

        if ($country){
            $qb->andWhere('base_tour.country = :country');
            $qb->setParameter('country', $country );
        }

        if ($city){
            $qb->andWhere("base_tour.city IN ($city)");
        }

        $qb
            ->andWhere("base_tour.appStatus = :appStatus")
            ->setParameter("appStatus", $inAppStatus)
            ->orderBy("base_tour.createdAt","DESC");

        return $qb;
    }
}

