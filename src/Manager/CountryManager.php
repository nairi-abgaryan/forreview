<?php

namespace App\Manager;

use App\Entity\Country;
use App\Entity\CountryLang;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;

class CountryManager
{
    /**
     * @var CountryRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * CountryManager constructor.
     *
     * @param CountryRepository $repository
     * @param EntityManagerInterface  $em
     */
    public function __construct(CountryRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @param $id
     *
     * @return object|null|Country
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|Country
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
     * @return Country
     */
    public function create()
    {
        return new Country();
    }

    /**
     * @param array $countries
     * @return mixed
     */
    public function persist(array $countries)
    {
        foreach ($countries as $lang => $items)
        {
            foreach ($items['countries'] as $key => $item)
            {
                $country = $this->findOneBy(['iso' => $key]);
                $countryLang = new CountryLang();
                $countryLang->setLang($lang);
                $countryLang->setName($item['name']);
                $countryLang->setCountry($country);
                $country->setCountryLang([$countryLang]);
                $this->em->persist($country);
            }
        }
        $this->em->flush();

        return $countries;
    }

    /**
     * @param Country $country
     *
     * @return string
     */
    public function remove(Country $country)
    {
        $this->em->remove($country);
        $this->em->flush();

        return true;
    }
}

