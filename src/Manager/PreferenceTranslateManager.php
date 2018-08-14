<?php

namespace App\Manager;

use App\Entity\PreferenceLang;
use App\Repository\PreferenceLangRepository;
use Doctrine\ORM\EntityManagerInterface;

class PreferenceTranslateManager
{
    /**
     * @var PreferenceLangRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Preference Category constructor.
     *
     * @param PreferenceLangRepository $repository
     * @param EntityManagerInterface  $em
     */
    public function __construct(PreferenceLangRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @param $id
     *
     * @return object|null|PreferenceLang
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|PreferenceLang
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
        return $this->repository->findAll();
    }

    /**
     * @return PreferenceLang
     */
    public function create()
    {
        return new PreferenceLang();
    }

    /**
     * @param PreferenceLang $preferenceTranslate
     * @return mixed
     */
    public function persist(PreferenceLang $preferenceTranslate)
    {
        $this->em->persist($preferenceTranslate);
        $this->em->flush();

        return $preferenceTranslate;
    }
}
