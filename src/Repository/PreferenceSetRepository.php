<?php

namespace App\Repository;

use App\Entity\PreferenceSet;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PreferenceSetRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PreferenceSet::class);
    }

    /**
     * @param $filter
     * @return mixed
     */
    public function findAllLangList($filter)
    {
        $qb = $this->createQueryBuilder("preference_set")
            ->leftJoin("App:PreferenceSetLang","psl","WITH","psl.preferenceSet=preference_set")
            ->leftJoin("App:Preference","p","WITH","p.preferenceSet=preference_set")
            ->leftJoin("App:PreferenceLang","pl","WITH","pl.preference=p")
            ->leftJoin("preference_set.preferenceTag","ps")
            ->groupBy("preference_set.id")
        ;

        if (isset($filter['preference_tags'])) {
            $trip_types = '('.$filter['preference_tags'].')';
            $qb
                ->andWhere("ps.id in $trip_types")
            ;
        }

        $result =  $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);

        return $result;
    }

    /**
     * @param PreferenceSet $preferenceSet
     * @return mixed
     */
    public function findTranslates(PreferenceSet $preferenceSet)
    {
        $this->_em->getFilters()->disable("locale_filter");

        return $this->getEntityManager()->createQueryBuilder()
                ->select("al.lang")
                ->from("PreferenceSetLang.php", "al")
                ->where("al.preferenceSet = :preferenceSet")
                ->setParameter("preferenceSet", $preferenceSet)
                ->getQuery()
                ->getResult()
        ;
    }
}
