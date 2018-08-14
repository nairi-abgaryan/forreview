<?php

namespace App\Repository;

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, City::class);
    }

    /**
     * @param $query
     * @param $country
     * @return QueryBuilder
     */
    public function search($query, $country)
    {
        $qb =
            $this->createQueryBuilder("city")
                        ->leftJoin("city.country", "country")
                        ->leftJoin("city.cityLang", "cityLang");


        if ($country){
            $qb
                ->leftJoin("country.countryLang", "countryLang")
                ->andWhere("countryLang.name = :country")
                ->setParameter("country", $country);
        }
        if ($query) {
            $qb->andWhere('cityLang.name LIKE :query');
            $qb->setParameter('query', "%" . $query . "%");
        }

        return $qb->getQuery()->getResult();
    }
}
