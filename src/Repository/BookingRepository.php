<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Tour;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * @param User|null $user
     * @param User|null $expert
     * @param $expertStatus
     * @param $touristStatus
     * @return mixed
     */
    public function search(User $user = null, User $expert = null, $expertStatus, $touristStatus)
    {
        $qb = $this->createQueryBuilder("booking");

        if ($user){
            $qb->andWhere("booking.owner = :user");
            $qb->setParameter("user", $user);
        }

        if ($expert){
            $qb->andWhere("booking.expert = :expert");
            $qb->setParameter("expert", $expert);
        }

        if ($expertStatus){
            $qb->andWhere("booking.expertStatus = :expertStatus");
            $qb->setParameter("expertStatus", $expertStatus);
        }

        if ($touristStatus){
            $qb->andWhere("booking.touristStatus = :touristStatus");
            $qb->setParameter("touristStatus", $touristStatus);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param User|null $user
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function myList(User $user = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $reactedFriendsQb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select("user, partial next_experiences.{id}, partial notRatedExperiences.{id}")
            ->from("App:User", "user")
            ->where("user = :owner")
            ->leftJoin('user.nextExperiences', 'next_experiences')
            ->setMaxResults(4)
            ->leftJoin('user.enquiries', 'enquiries')
            ->setMaxResults(4)
            ->leftJoin('user.notRatedExperiences', 'notRatedExperiences', Query\Expr\Join::WITH, $qb->expr()->in('notRatedExperiences', $reactedFriendsQb
                    ->select('notRatedExperience')
                    ->from('App:Booking', 'notRatedExperience')
                    ->where('notRatedExperience.isRate = :rated')
                    ->andWhere('notRatedExperience.owner = :owner')
                    ->innerJoin("App:Tour", "tour", "WITH", "tour.status = :finishStatus")
                    ->setMaxResults(4)
                    ->getQuery()
                    ->getDQL()
                ))
            ->setMaxResults(4)
            ->setParameter("owner", $user)
            ->setParameter("rated", false)
            ->setParameter("finishStatus", Tour::FINISHED)
        ;

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_OBJECT);
    }
}

