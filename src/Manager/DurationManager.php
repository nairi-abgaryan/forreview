<?php

namespace App\Manager;

use App\Entity\Duration;
use App\Repository\DurationRepository;
use Doctrine\ORM\EntityManagerInterface;

class DurationManager
{
    /**
     * @var DurationRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DurationManager constructor.
     *
     * @param DurationRepository $repository
     * @param EntityManagerInterface  $em
     */
    public function __construct(DurationRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @param $id
     *
     * @return object|null|Duration
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|Duration
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
     * @return Duration
     */
    public function create()
    {
        return new Duration();
    }

    /**
     * @param Duration $duration
     *
     * @return string
     */
    public function remove(Duration $duration)
    {
        $this->em->remove($duration);
        $this->em->flush();

        return true;
    }
}

