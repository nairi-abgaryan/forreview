<?php

namespace App\Manager;

use App\Entity\Language;
use App\Entity\LanguageLang;
use App\Repository\LanguageRepository;
use Doctrine\ORM\EntityManagerInterface;

class LanguageManager
{
    /**
     * @var LanguageRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Language constructor.
     *
     * @param LanguageRepository $repository
     * @param EntityManagerInterface  $em
     */
    public function __construct(LanguageRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @param $id
     *
     * @return object|null|Language
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|Language
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
        $qb = $this->repository->createQueryBuilder('language');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Language
     */
    public function create()
    {
        return new Language();
    }

    /**
     * @param array $language
     * @return mixed
     */
    public function persist(array $language)
    {
        foreach ($language as $lang => $items)
        {
            foreach ($items as $key => $item)
            {
                $language = $this->findOneBy(['shortName' => $lang]);
                if (!$language){
                    $language = new Language();
                    $language->setTranslates([$lang]);
                    $language->setShortName($key);
                }
                $languageLang = new LanguageLang();
                $languageLang->setLang($lang);
                $languageLang->setName($item['name']);
                $languageLang->setLanguage($language);
                $language->setLanguageLang([$languageLang]);
                $this->em->persist($language);
            }
        }
        $this->em->flush();

        return $language;
    }

    /**
     * @param Language $language
     *
     * @return string
     */
    public function remove(Language $language)
    {
        $this->em->remove($language);
        $this->em->flush();

        return true;
    }
}

