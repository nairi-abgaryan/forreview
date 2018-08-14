<?php

namespace App\Manager;

use App\Entity\PreferenceTag;
use App\Repository\PreferenceTagRepository;
use Doctrine\ORM\EntityManagerInterface;

class PreferenceTagManager
{
    /**
     * @var PreferenceTagRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * PreferenceTagManager constructor.
     *
     * @param PreferenceTagRepository $repository
     * @param EntityManagerInterface  $em
     */
    public function __construct(PreferenceTagRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @param $id
     *
     * @return object|null|PreferenceTag
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|PreferenceTag
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
     * @return PreferenceTag
     */
    public function create()
    {
        return new PreferenceTag();
    }

    /**
     * @param PreferenceTag $preferenceTag
     * @return PreferenceTag
     */
    public function persist(PreferenceTag $preferenceTag)
    {
        $this->em->persist($preferenceTag);
        $this->em->flush();

        return $preferenceTag;
    }

    /**
     * @param PreferenceTag $preferenceTag
     *
     * @return string
     */
    public function remove(PreferenceTag $preferenceTag)
    {
        $this->em->remove($preferenceTag);
        $this->em->flush();

        return true;
    }

    /**
     * @param PreferenceTag $data
     * @param $lang
     * @return PreferenceTag
     */
    public function createPreferenceTag(PreferenceTag $data, $lang)
    {
        $data->setTranslates([$lang]);
        $data->addPreferenceTagLang($data, $lang);
        return $this->persist($data);
    }

    /**
     * @param PreferenceTag $data
     * @param $currentLang
     * @return PreferenceTag
     */
    public function updatePreferenceTag(PreferenceTag $data, $currentLang)
    {
        $lang = $data->getTranslates();
        if(!in_array($currentLang, $data->getTranslates())){
            array_push($lang, $currentLang);
            $data->setTranslates($lang);
        }

        $data->addPreferenceTagLang($data, $currentLang);
        return $this->persist($data);
    }
}

