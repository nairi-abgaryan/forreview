<?php

namespace App\Manager;

use App\Entity\Activity;
use App\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\ValidatorBuilder;

class ActivityManager
{
    /**
     * @var ActivityRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ActivityRepository constructor.
     *
     * @param ActivityRepository $repository
     * @param EntityManagerInterface  $em
     */
    public function __construct(ActivityRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @param $id
     *
     * @return object|null|Activity
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|Activity
     */
    public function findOneBy($criteria = [])
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @return Activity[]
     */
    public function findAll()
    {
        $qb = $this->repository->findAll();

        return $qb;
    }

    /**
     * @return Activity
     */
    public function create()
    {
        return new Activity();
    }

    /**
     * @param Activity $activity
     * @return mixed
     */
    public function persist(Activity $activity)
    {
        $this->em->persist($activity);
        $this->em->flush();

        return $activity;
    }

    /**
     * @param Activity $data
     * @param $lang
     * @return mixed
     */
    public function createActivity(Activity $data, $lang)
    {
        $data->setTranslates([$lang]);
        $data->addActivityLang($data, $lang);

        return $this->persist($data);
    }

    /**
     * @param Activity $data
     * @param $currentLang
     * @return mixed
     */
    public function updateActivity(Activity $data, $currentLang)
    {
        $lang = $data->getTranslates();
        $data->addActivityLang($data, $currentLang);

        if(!in_array($currentLang, $data->getTranslates())){
            array_push($lang, $currentLang);
            $data->setTranslates($lang);
        }

        return $this->persist($data);
    }

    /**
     * @param Activity $activity
     *
     * @return string
     */
    public function remove(Activity $activity)
    {
        $this->em->remove($activity);
        $this->em->flush();

        return true;
    }
}
