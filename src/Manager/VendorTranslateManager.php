<?php

namespace App\Manager;

use App\Entity\VendorLang;
use App\Repository\VendorLangRepository;
use Doctrine\ORM\EntityManagerInterface;

class VendorTranslateManager
{
    /**
     * @var VendorLangRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * VendorManager constructor.
     *
     * @param VendorLangRepository $repository
     * @param EntityManagerInterface  $em
     */
    public function __construct(VendorLangRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @param $id
     *
     * @return object|null|VendorLang
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|VendorLang
     */
    public function findOneBy($criteria = [])
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @return mixed
     */
    public function findAll()
    {
        $qb = $this->repository->findAll();

        return $qb;
    }

    /**
     * @param $lang
     * @param $user
     * @return array
     */
    public function findAllLangList($lang, $user)
    {
        $qb = $this->repository->findAllLangList($lang, $user);

        return $qb;
    }

    /**
     * @return VendorLang
     */
    public function create()
    {
        return new VendorLang();
    }

    /**
     * @param VendorLang $vendorLang
     * @return VendorLang
     */
    public function persist(VendorLang $vendorLang)
    {
        $this->em->persist($vendorLang);
        $this->em->flush();

        return $vendorLang;
    }

    /**
     * @param VendorLang $vendorLang
     *
     * @return string
     */
    public function remove(VendorLang $vendorLang)
    {
        $this->em->remove($vendorLang);
        $this->em->flush();

        return true;
    }
}

