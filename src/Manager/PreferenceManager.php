<?php

namespace App\Manager;

use App\Entity\Preference;
use App\Repository\PreferenceRepository;
use Doctrine\ORM\EntityManagerInterface;

class PreferenceManager
{
    /**
     * @var PreferenceRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * UserManager constructor.
     *
     * @param PreferenceRepository $repository
     * @param EntityManagerInterface  $em
     */
    public function __construct(PreferenceRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @param $id
     *
     * @return object|null|Preference
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|Preference
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
        return $this->repository->createQueryBuilder("preference")
            ->orderBy("preference.preferenceSet")
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array
     */
    public function findAllLangList()
    {
        $qb = $this->repository->findAllLangList();

        return $qb;
    }

    /**
     * @return Preference
     */
    public function create()
    {
        return new Preference();
    }

    /**
     * @param Preference $preference
     * @return Preference
     */
    public function persist(Preference $preference)
    {
        $this->em->persist($preference);
        $this->em->flush();

        return $preference;
    }

    /**
     * @param Preference $preference
     *
     * @return string
     */
    public function remove(Preference $preference)
    {
        $this->em->remove($preference);
        $this->em->flush();

        return true;
    }

    /**
     * @param Preference $data
     * @param $lang
     * @return Preference
     */
    public function createPreference(Preference $data, $lang)
    {
        $data->setTranslates([$lang]);
        $data->addPreferenceLang($data, $lang);
        return $this->persist($data);
    }

    /**
     * @param Preference $data
     * @param $currentLang
     * @return Preference
     */
    public function updatePreference(Preference $data, $currentLang)
    {
        $lang = $data->getTranslates();
        if(!in_array($currentLang, $data->getTranslates())){
            array_push($lang, $currentLang);
            $data->setTranslates($lang);
        }

        $data->addPreferenceLang($data, $currentLang);
        return $this->persist($data);
    }
}
