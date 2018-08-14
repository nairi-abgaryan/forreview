<?php

namespace App\Manager;

use App\Entity\Time;
use App\Repository\TimeRepository;
use Doctrine\ORM\EntityManagerInterface;

class TimeManager
{
    /**
     * @var TimeRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * TimeManager constructor.
     *
     * @param TimeRepository $repository
     * @param EntityManagerInterface  $em
     */
    public function __construct(TimeRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @param $id
     *
     * @return object|null|Time
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|Time
     */
    public function findOneBy($criteria = [])
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @return array
     */
    public function findAll()
    {
        $qb = $this->repository->findAll();

        return $qb;
    }

    /**
     * @return Time
     */
    public function create()
    {
        return new Time();
    }

    /**
     * @param Time $time
     *
     * @return string
     */
    public function remove(Time $time)
    {
        $this->em->remove($time);
        $this->em->flush();

        return true;
    }
}

