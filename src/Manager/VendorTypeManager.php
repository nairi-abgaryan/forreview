<?php

namespace App\Manager;

use App\Entity\VendorType;
use App\Repository\VendorTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class VendorTypeManager
{
    /**
     * @var VendorTypeRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * VendorTypeManager constructor.
     *
     * @param VendorTypeRepository $repository
     * @param EntityManagerInterface  $em
     */
    public function __construct(VendorTypeRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @param $query
     * @param $inAppStatus
     * @return QueryBuilder
     */
    public function search($query, $inAppStatus)
    {
        $qb = $this->repository->search($query, $inAppStatus);

        return $qb;
    }

    /**
     * @param $id
     *
     * @return object|null|VendorType
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|VendorType
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
     * @return VendorType
     */
    public function create()
    {
        return new VendorType();
    }

    /**
     * @param VendorType $vendorType
     * @return VendorType
     */
    public function persist(VendorType $vendorType)
    {
        $this->em->persist($vendorType);
        $this->em->flush();

        return $vendorType;
    }

    /**
     * @param VendorType $vendorType
     *
     * @return string
     */
    public function remove(VendorType $vendorType)
    {
        $this->em->remove($vendorType);
        $this->em->flush();

        return true;
    }
}

