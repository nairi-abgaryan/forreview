<?php

namespace App\Manager;

use App\Entity\Vendor;
use App\Repository\VendorRepository;
use App\Resource\Constant;
use App\Service\StatusService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class VendorManager
{

    /**
     * @var VendorRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CityManager $cityManager
     */
    private $cityManager;

    /**
     * @var StatusService $statusService
     */
    private $statusService;

    /**
     * VendorManager constructor.
     *
     * @param VendorRepository $repository
     * @param EntityManagerInterface  $em
     * @param CityManager  $cityManager
     * @param StatusService  $statusService
     */
    public function __construct
    (
        VendorRepository $repository,
        EntityManagerInterface $em,
        CityManager $cityManager,
        StatusService $statusService
    )
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->cityManager = $cityManager;
        $this->statusService = $statusService;
    }

    /**
     * @param $id
     *
     * @return object|null|Vendor
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|Vendor
     */
    public function findOneBy($criteria = [])
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @param $query
     * @param $status
     * @param $inAppStatus
     * @param $activity
     * @param $vendorType
     * @param $country
     * @param $city
     * @return QueryBuilder
     */
    public function search($query, $status, $inAppStatus, $activity, $vendorType, $country, $city)
    {
        $qb = $this->repository->search($query, $status, $inAppStatus, $activity, $vendorType, $country, $city);

        return $qb;
    }

    /**
     * @return Vendor
     */
    public function create()
    {
        return new Vendor();
    }

    /**
     * @param Vendor $vendor
     *
     * @return string
     */
    public function remove(Vendor $vendor)
    {
        $this->em->remove($vendor);
        $this->em->flush();

        return true;
    }

    /**
     * @param Vendor $vendor
     * @return Vendor
     */
    public function persist(Vendor $vendor)
    {
        $this->em->persist($vendor);
        $this->em->flush();

        return $vendor;
    }

    /**
     * @param Vendor $vendor
     * @param  $lang
     * @return Vendor
     */
    public function createVendor(Vendor $vendor, $lang)
    {
        $vendor = $this->checkTranslation($vendor, $lang);
        $vendor->addVendorLang($vendor, $lang);
        $vendor = $this->cityManager->addCity($vendor);
        $vendor = $this->statusService->changeAppStatus($vendor, Constant::PUBLISHED);
        $vendor = $this->statusService->changeStatus($vendor, Constant::ACTIVE);

        return $this->persist($vendor);
    }


    /**
     * @param Vendor $vendor
     * @param $lang
     * @return Vendor|mixed
     */
    public function checkTranslation(Vendor $vendor, $lang)
    {
        if (!$vendor->getTranslates()){
            $vendor->setTranslates([$lang]);
        }

        if(!in_array($lang, $vendor->getTranslates())){
            $langArray = $vendor->getTranslates();
            array_push($langArray, $lang);
            $vendor->setTranslates($langArray);
        }

        return $vendor;
    }
}

