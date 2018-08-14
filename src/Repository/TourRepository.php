<?php

namespace App\Repository;

use App\Entity\Tour;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Tour|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tour|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tour[]    findAll()
 * @method Tour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TourRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Tour::class);
    }

    /**
     * @param $query
     * @param $status
     * @param $minPrice
     * @param $maxPrice
     * @param $city
     * @param $country
     * @param $inAppStatus
     * @return QueryBuilder
     */
    public function findAllList($query, $status, $inAppStatus, $minPrice, $maxPrice, $city, $country)
    {
        $qb = $this->createQueryBuilder("tour")
            ->leftJoin('tour.tourLang', 'tourLang')
            ->leftJoin('tour.baseTour', 'baseTour');

        if ($query){
            $qb
                ->andWhere('tourLang.title LIKE :query')
                ->setParameter('query', "%" . $query . "%");
        }

        if ($country){
            $qb->andWhere('baseTour.country = :country');
            $qb->setParameter('country', $country );
        }

        if ($city){
            $qb->andWhere("baseTour.city IN ($city)");
        }

        if ($maxPrice){
            $qb
                ->andWhere('tour.price < :max_price')
                ->setParameter('max_price', $maxPrice );
        }

        if ($minPrice){
            $qb
                ->andWhere('tour.price > :min_price')
                ->setParameter('min_price', $minPrice);
        }

        $qb
            ->andWhere("tour.appStatus = :appStatus")
            ->setParameter("appStatus", $inAppStatus)
            ->andWhere("tour.status = :status")
            ->setParameter("status", $status)
            ->orderBy("tour.createdAt","DESC");

        return $qb;
    }

    /**
     * @param User|null $user
     * @return mixed
     */
    public function myList(User $user = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select("partial user.{id}, tours")
            ->from("App:User", "user")
            ->where("user = :owner")
            ->leftJoin('user.tours', 'tours',"WITH", "tours.owner = :owner")
            ->setMaxResults(4)
            ->leftJoin('user.enquiries', 'enquiries',"WITH", "enquiries.owner = :owner")
            ->setMaxResults(4)
            ->setParameter("owner", $user)
        ;

        return $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);
    }
}
