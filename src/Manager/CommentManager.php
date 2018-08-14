<?php

namespace App\Manager;

use App\Entity\Comment;
use App\Entity\User;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;

class CommentManager
{
    /**
     * @var CommentRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var BookingManager
     */
    private $bookingManager;

    /**
     * CommentRepository constructor.
     *
     * @param CommentRepository $repository
     * @param EntityManagerInterface  $em
     * @param BookingManager  $bookingManager
     */
    public function __construct
    (
        CommentRepository $repository,
        EntityManagerInterface $em,
        BookingManager $bookingManager
    )
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->bookingManager = $bookingManager;
    }

    /**
     * @param $id
     *
     * @return object|null|Comment
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|Comment
     */
    public function findOneBy($criteria = [])
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @return Comment[]
     */
    public function findAll()
    {
        $qb = $this->repository->findAll();

        return $qb;
    }

    /**
     * @return Comment
     */
    public function create()
    {
        return new Comment();
    }

    /**
     * @param Comment $comment
     * @return mixed
     */
    public function persist(Comment $comment)
    {
        $this->em->persist($comment);
        $this->em->flush();

        return $comment;
    }

    /**
     * @param Comment $comment
     * @param User $user
     * @return mixed
     */
    public function createComment(Comment $comment, User $user)
    {
        $order = $this->bookingManager->findOneBy(["tour" => $comment->getTour(), "owner" => $user]);
        if (!$order){
           return false;
        }

        $comment->setOwner($user);

        return $this->persist($comment);
    }
}

