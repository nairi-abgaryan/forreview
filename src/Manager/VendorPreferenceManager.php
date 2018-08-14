<?php

namespace App\Manager;

use App\Entity\Vendor;
use App\Entity\VendorPreference;
use App\Repository\VendorPreferenceRepository;
use Doctrine\ORM\EntityManagerInterface;

class VendorPreferenceManager
{
    /**
     * @var VendorPreferenceRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * VendorPreferenceManager constructor.
     *
     * @param VendorPreferenceRepository $repository
     * @param EntityManagerInterface  $em
     */
    public function __construct(VendorPreferenceRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @param $id
     *
     * @return object|null|VendorPreference
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|VendorPreference
     */
    public function findOneBy($criteria = [])
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @return VendorPreference
     */
    public function create()
    {
        return new VendorPreference();
    }

    /**
     * @param array $vendorPreferences
     * @return mixed
     */
    public function persist(array $vendorPreferences)
    {
        foreach ($vendorPreferences['preferences'] as $item)
        {
            $vendorPreference = $this->create();
            $vendorPreference->setVendor($vendorPreferences['vendor']);
            $vendorPreference->setPreferences($item);
            $this->em->persist($vendorPreference);
        }

        $this->em->flush();

        return true;
    }

    /**
     * @param VendorPreference $vendorPreference
     *
     * @return string
     */
    public function remove(VendorPreference $vendorPreference)
    {
        $this->em->remove($vendorPreference);
        $this->em->flush();

        return true;
    }

    /**
     * @param Vendor $vendor
     */
    public function removeRows(Vendor $vendor)
    {
        $query = $this->em->createQuery("DELETE App:VendorPreference vp WHERE vp.vendor = :vendor");
        $query->setParameter("vendor", $vendor);
        $query->execute();
    }
}
