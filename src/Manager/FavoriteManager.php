<?php

namespace App\Manager;

use App\Entity\Favorite;
use App\Entity\User;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;

class FavoriteManager
{
    /**
     * @var FavoriteRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ActivityRepository constructor.
     *
     * @param FavoriteRepository $repository
     * @param EntityManagerInterface  $em
     */
    public function __construct(FavoriteRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @param $id
     *
     * @return object|null|Favorite
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|Favorite
     */
    public function findOneBy($criteria = [])
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @param User $user
     * @return Favorite[]
     */
    public function findBy(User $user)
    {
        $qb = $this->repository->findBy(["owner" => $user]);

        return $qb;
    }

    /**
     * @return Favorite
     */
    public function create()
    {
        return new Favorite();
    }

    /**
     * @param Favorite $favorite
     * @return mixed
     */
    public function persist(Favorite $favorite)
    {
        $this->em->persist($favorite);
        $this->em->flush();

        return $favorite;
    }

    /**
     * @param Favorite $favorite
     *
     * @return string
     */
    public function remove(Favorite $favorite)
    {
        $this->em->remove($favorite);
        $this->em->flush();

        return true;
    }
}
