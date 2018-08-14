<?php

namespace App\Manager;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;

class RoleManager
{
    /**
     * @var RoleRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * RoleManager constructor.
     *
     * @param RoleRepository $repository
     * @param EntityManagerInterface  $em
     */
    public function __construct(RoleRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @param $id
     *
     * @return object|null|Role
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|Role
     */
    public function findOneBy($criteria = [])
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|Role
     */
    public function findBy($criteria = [])
    {
        return $this->repository->findBy($criteria);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findAll()
    {
        $qb = $this->repository->createQueryBuilder('Role');

        return $qb;
    }

    /**
     * @return Role
     */
    public function create()
    {
        return new Role();
    }

    /**
     * @param Role $Role
     * @return Role
     */
    public function persist(Role $Role)
    {
        $this->em->persist($Role);
        $this->em->flush();

        return $Role;
    }
}
