<?php

namespace App\Manager;

use App\Entity\Discount;
use App\Repository\DiscountRepository;
use Doctrine\ORM\EntityManagerInterface;

class DiscountManager
{
    /**
     * @var DiscountRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DiscountManager constructor.
     *
     * @param DiscountRepository $repository
     * @param EntityManagerInterface  $em
     */
    public function __construct(DiscountRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @param $id
     *
     * @return object|null|Discount
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|Discount
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
     * @return Discount
     */
    public function create()
    {
        return new Discount();
    }

    /**
     * @param Discount $discount
     *
     * @return string
     */
    public function remove(Discount $discount)
    {
        $this->em->remove($discount);
        $this->em->flush();

        return true;
    }
}

