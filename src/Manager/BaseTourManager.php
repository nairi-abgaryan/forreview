<?php

namespace App\Manager;

use App\Entity\BaseTour;
use App\Repository\BaseTourRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class BaseTourManager
{
    /**
     * @var BaseTourRepository
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
     * BaseTour constructor.
     *
     * @param BaseTourRepository $repository
     * @param EntityManagerInterface  $em
     * @param CityManager $cityManager
     */
    public function __construct
    (
        BaseTourRepository $repository,
        EntityManagerInterface $em,
        CityManager $cityManager
    )
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->cityManager = $cityManager;
    }

    /**
     * @param $id
     *
     * @return object|null|BaseTour
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|BaseTour
     */
    public function findOneBy($criteria = [])
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @param $query
     * @param $inAppStatus
     * @param $activity
     * @param $city
     * @param $country
     * @return QueryBuilder
     */
    public function search($query, $inAppStatus, $activity, $city, $country)
    {
        $qb = $this->repository->search($query, $inAppStatus, $activity, $city, $country);

        return $qb;
    }

    /**
     * @return BaseTour
     */
    public function create()
    {
        return new BaseTour();
    }

    /**
     * @param BaseTour $BaseTour
     * @return mixed
     */
    public function persist(BaseTour $BaseTour)
    {
        $this->em->persist($BaseTour);
        $this->em->flush();

        return $BaseTour;
    }

    /**
     * @param BaseTour $BaseTour
     *
     * @return string
     */
    public function remove(BaseTour $BaseTour)
    {
        $this->em->remove($BaseTour);
        $this->em->flush();

        return true;
    }

    /**
     * @param BaseTour $data
     * @param $lang
     * @return mixed
     */
    public function createBaseTour(BaseTour $data, $lang)
    {
        $data->setTranslates([$lang]);
        $data->addBaseTourLang($data, $lang);
        $this->cityManager->addCity($data);

        return $this->persist($data);
    }

    /**
     * @param BaseTour $data
     * @param $currentLang
     * @return mixed
     */
    public function updateBaseTour(BaseTour $data, $currentLang)
    {
        $lang = $data->getTranslates();
        $data->addBaseTourLang($data, $currentLang);

        if(!in_array($currentLang, $data->getTranslates())){
            array_push($lang, $currentLang);
            $data->setTranslates($lang);
        }

        $this->cityManager->addCity($data);

        return $this->persist($data);
    }
}
