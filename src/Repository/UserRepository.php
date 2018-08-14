<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param $query
     * @param $inAppStatus
     * @param $status
     * @param $role
     * @return QueryBuilder
     */
    public function search($query, $inAppStatus, $status, $role, $country, $city)
    {
        $qb = $this->createQueryBuilder("user")
            ->leftJoin('user.userLang', 'userLang');

        if ($query) {
            $qb->orWhere('userLang.firstName LIKE :query');
            $qb->orWhere('userLang.lastName LIKE :query');
            $qb->orWhere('user.phone LIKE :query');
            $qb->orWhere('user.email LIKE :query');
            $qb->setParameter('query', "%" . $query . "%");
        }

        if (!is_null($status)) {
            $qb->leftJoin("user.expert", "expert");
            $qb->andWhere("expert.status = :status");
            $qb->setParameter("status", $status);
        }

        if ($role) {
            $qb->leftJoin("user.roles", "role");
            $qb->andWhere("role = :role");
            $qb->setParameter("role", $role);
        }

        if ($country){
            $qb->andWhere('user.country = :country');
            $qb->setParameter('country', $country );
        }

        if ($city){
            $qb->andWhere("user.city IN ($city)");
        }

        $qb
            ->andWhere("user.appStatus = :appStatus")
            ->setParameter("appStatus", $inAppStatus)
            ->orderBy("user.createdAt","DESC");

        return $qb;
    }

    /**
     * @param array $criteria
     * @return array
     */
    public function findByByEmailAndIsActiveCriteria($criteria)
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.email = :email')
            ->setParameter('email', $criteria['email'])
            ->andWhere('user.isActive = :isActive')
            ->setParameter('isActive', 1)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Criteria
     */
    static public function createEnquiriesCriteria()
    {
        return Criteria::create()
            ->where(Criteria::expr()->eq('touristStatus', Booking::PENDING))
            ->orWhere(Criteria::expr()->eq('expertStatus', Booking::PENDING))
            ->setMaxResults(1)
            ->orderBy(["id" => "desc"])
            ;
    }

    /**
     * @return Criteria
     */
    static public function createNextExperiences()
    {
        return Criteria::create()
            ->where(Criteria::expr()->eq('touristStatus', Booking::APPROVE))
            ->orWhere(Criteria::expr()->eq('expertStatus', Booking::APPROVE))
            ;
    }

    /**
     * @return Criteria
     */
    static public function notRatedExperiences()
    {
        return Criteria::create()
            ->where(Criteria::expr()->eq('isRate', false))
            ;
    }
}
