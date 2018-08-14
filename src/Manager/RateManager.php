<?php

namespace App\Manager;

use App\Entity\Rate;
use App\Entity\User;
use App\Repository\RateRepository;
use Doctrine\ORM\EntityManagerInterface;

class RateManager
{
    /**
     * @var RateRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var BookingManager
     */
    private $bookingManager;

    /**
     * RateRepository constructor.
     *
     * @param RateRepository $repository
     * @param EntityManagerInterface  $em
     * @param BookingManager  $bookingManager
     */
    public function __construct
    (
        RateRepository $repository,
        EntityManagerInterface $em,
        BookingManager $bookingManager
    )
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->bookingManager = $bookingManager;
    }

    /**
     * @param $id
     *
     * @return object|null|Rate
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|Rate
     */
    public function findOneBy($criteria = [])
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @return Rate[]
     */
    public function findAll()
    {
        $qb = $this->repository->findAll();

        return $qb;
    }

    /**
     * @return Rate
     */
    public function create()
    {
        return new Rate();
    }

    /**
     * @param Rate $rate
     * @return mixed
     */
    public function persist(Rate $rate)
    {
        $this->em->persist($rate);
        $this->em->flush();

        return $rate;
    }

    /**
     * @param Rate $rate
     * @param User $user
     * @return mixed
     */
    public function createRate(Rate $rate, User $user)
    {
        $order = $this->bookingManager->findOneBy(["tour" => $rate->getTour(), "owner" => $user]);

        if (!$order){
           return false;
        }

        $order->setIsRate(true);
        $this->bookingManager->persist($order);
        $rate->setOwner($user);

        return $this->persist($rate);
    }
}

