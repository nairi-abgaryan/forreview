<?php

namespace App\Repository;

use App\Entity\Preference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PreferenceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Preference::class);
    }


    /**
     * @return mixed
     */
    public function findAllLangList()
    {

        return $this->getEntityManager()->createQueryBuilder()
            ->select("p as preference")
            ->from("App:Preference","p")
            ->getQuery()
            ->getResult()
            ;
    }
}
