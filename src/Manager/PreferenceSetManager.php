<?php

namespace App\Manager;

use App\Entity\PreferenceSet;
use App\Repository\PreferenceSetRepository;
use Doctrine\ORM\EntityManagerInterface;

class PreferenceSetManager
{
    /**
     * @var PreferenceSetRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * PreferenceSet constructor.
     *
     * @param PreferenceSetRepository $repository
     * @param EntityManagerInterface  $em
     */
    public function __construct(PreferenceSetRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @param $id
     *
     * @return object|null|PreferenceSet
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param $filter
     * @param $user
     * @return array
     */
    public function findAllLangList($filter, $user)
    {
        $qb = $this->repository->findAllLangList($filter, $user);

        return $qb;
    }

    /**
     * @param array $criteria
     *
     * @return object|null|PreferenceSet
     */
    public function findOneBy($criteria = [])
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findAll()
    {
        $qb = $this->repository->createQueryBuilder('activity');

        return $qb;
    }

    /**
     * @return PreferenceSet
     */
    public function create()
    {
        return new PreferenceSet();
    }

    /**
     * @param PreferenceSet $preferenceSet
     * @return mixed
     */
    public function persist(PreferenceSet $preferenceSet)
    {
        $this->em->persist($preferenceSet);
        $this->em->flush();

        return $preferenceSet;
    }

    /**
     * @param PreferenceSet $preferenceSet
     *
     * @return string
     */
    public function remove(PreferenceSet $preferenceSet)
    {
        $this->em->remove($preferenceSet);
        $this->em->flush();

        return true;
    }

    /**
     * @param PreferenceSet $data
     * @param $lang
     * @return PreferenceSet
     */
    public function createPreferenceSet(PreferenceSet $data, $lang)
    {
        $data->setTranslates([$lang]);
        $data->addPreferenceSetLang($data, $lang);
        return $this->persist($data);
    }

    /**
     * @param PreferenceSet $data
     * @param $currentLang
     * @return PreferenceSet
     */
    public function updatePreferenceSet(PreferenceSet $data, $currentLang)
    {
        $lang = $data->getTranslates();
        if(!in_array($currentLang, $data->getTranslates())){
            array_push($lang, $currentLang);
            $data->setTranslates($lang);
        }

        $data->addPreferenceSetLang($data, $currentLang);
        return $this->persist($data);
    }
}

