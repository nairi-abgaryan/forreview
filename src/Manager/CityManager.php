<?php

namespace App\Manager;

use App\Entity\City;
use App\Entity\CityLang;
use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class CityManager
{
    /**
     * @var CityRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var string
     */
    private $google_api_key;

    /**
     * @var string
     */
    private $google_url;

    /**
     * @var ArrayCollection
     */
    private $locale;

    /**
     * CityManager constructor.
     *
     * @param CityRepository $repository
     * @param EntityManagerInterface  $em
     * @param string  $google_api_key
     * @param string  $google_url
     * @param  $locale
     */
    public function __construct
    (
        CityRepository $repository,
        EntityManagerInterface $em,
        string $google_api_key,
        string $google_url,
        $locale
    )
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->google_api_key = $google_api_key;
        $this->google_url = $google_url;
        $this->locale = $locale;
    }

    /**
     * @param $id
     *
     * @return object|null|City
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|City
     */
    public function findOneBy($criteria = [])
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|City
     */
    public function findBy($criteria = [])
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
     * @return City
     */
    public function create()
    {
        return new City();
    }

    /**
     * @param City $city
     *
     * @return string
     */
    public function remove(City $city)
    {
        $this->em->remove($city);
        $this->em->flush();

        return true;
    }

    /**
     * @param $data
     * @return mixed $data
     */
    public function addCity($data)
    {
        $placeId = $data->getPlaceId();
        if (!$placeId) {
            return $data;
        }

        $city = $this->findOneBy(['placeId' => $placeId]);

        if (!$city){
            $city = $this->create();
            foreach ($this->locale as $lang){
                $outputGoogle = file_get_contents($this->google_url."details/json?placeid=$placeId&language=$lang&key=$this->google_api_key", false);
                $outputGoogle = json_decode($outputGoogle, true);
                $cityLang = new CityLang();
                $cityLang->setName($outputGoogle["result"]["name"]);
                $cityLang->setLang($lang);
                $cityLang->setCity($city);
                $city->addCityLang($cityLang);
            }

            $city->setPlaceId($placeId);
            $city->setCountry($data->getCountry());
            $city = $this->persist($city);
        }

        $data->setCity($city);

        return $data;
    }

    /**
     * @param $type
     * @param $city
     * @param $country
     * @return array|bool|\Doctrine\ORM\QueryBuilder|mixed|string
     */
    public function search($type, $city, $country)
    {
        if ($type != 'google' && $type) {
            return $this->repository->search($city, $country);
        }

        $outputGoogle = file_get_contents($this->google_url."autocomplete/json?input=$country+$city&country=Armenia&types=(cities)&key=$this->google_api_key");
        $outputGoogle = json_decode($outputGoogle, true);

        if($outputGoogle['status'] == 'ZERO_RESULTS') {
            return [];
        }

        $output = [];
        foreach ($outputGoogle['predictions'] as $key => $value){
                $output[$key]['place_id'] = $value["place_id"];
                $output[$key]['name'] = $value["terms"][0]["value"];
        }

        return $output;
    }

    /**
     * @param City $city
     * @return City
     */
    public function persist(City $city)
    {
        $this->em->persist($city);
        $this->em->flush();

        return $city;
    }
}

