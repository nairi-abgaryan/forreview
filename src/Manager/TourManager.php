<?php

namespace App\Manager;

use App\Entity\Tour;
use App\Entity\User;
use App\Repository\TourRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class TourManager
{
    /**
     * @var TourRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * TourRepository constructor.
     *
     * @param TourRepository $repository
     * @param EntityManagerInterface  $em
     */
    public function __construct(TourRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @param $id
     *
     * @return object|null|Tour
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|Tour
     */
    public function findOneBy($criteria = [])
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @return Tour[]
     */
    public function findAll()
    {
        $qb = $this->repository->findAll();

        return $qb;
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
        $qb = $this->repository->findAllList($query, $status, $inAppStatus, $minPrice, $maxPrice, $city, $country);

        return $qb;
    }

    /**
     * @return Tour
     */
    public function create()
    {
        return new Tour();
    }

    /**
     * @param Tour $tour
     * @return mixed
     */
    public function persist(Tour $tour)
    {
        $this->em->persist($tour);
        $this->em->flush();

        return $tour;
    }

    /**
     * @param User|null $user
     * @return mixed
     */
    public function myList(User $user = null)
    {
        return $this->repository->myList($user);
    }

    /**
     * @param Tour $tour
     *
     * @return string
     */
    public function remove(Tour $tour)
    {
        $this->em->remove($tour);
        $this->em->flush();

        return true;
    }
}
