<?php

namespace App\Manager;

use App\Entity\Booking;
use App\Entity\User;
use App\Repository\BookingRepository;
use App\Service\QrCodeService;
use Doctrine\ORM\EntityManagerInterface;

class BookingManager
{
    /**
     * @var BookingRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var QrCodeService
     */
    private $qrCodeService;

    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * BookingManager constructor.
     *
     * @param BookingRepository $repository
     * @param EntityManagerInterface  $em
     * @param QrCodeService  $qrCodeService
     * @param ImageManager  $imageManager
     */
    public function __construct
    (
        BookingRepository $repository,
        EntityManagerInterface $em,
        QrCodeService $qrCodeService,
        ImageManager $imageManager
    )
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->qrCodeService = $qrCodeService;
        $this->imageManager = $imageManager;
    }

    /**
     * @param $id
     *
     * @return object|null|Booking
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param User|null $user
     * @param $expertStatus
     * @param $touristStatus
     * @return mixed
     */
    public function search(User $user = null, $expertStatus, $touristStatus)
    {
        if ($user->getRole()->getName() == "ROLE_EXPERT"){
            return $this->repository->search(null, $user, $expertStatus, $touristStatus);
        }
        return $this->repository->search($user, null, $expertStatus, $touristStatus);
    }

    /**
     * @param User|null $user
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function myList(User $user = null)
    {
        return $this->repository->myList($user);
    }

    /**
     * @param Booking $data
     * @param User $user
     * @return mixed
     */
    public function createBooking(Booking $data, User $user)
    {
        $bytes = random_bytes(4);
        $code = bin2hex($bytes);
        $qrCode = $this->qrCodeService->makeQrCode($code);
        $data->setCode($code);
        $data->setQrCode($qrCode);
        $data->setOwner($user);
        $data->setExpert($data->getTour()->getOwner());
        $data->setCurrency("USD");
        $data->setExpertStatus(Booking::PENDING);
        $data->setTouristStatus(Booking::ENQUIRIES);
        $price =  $data->getTour()->getPrice() * $data->getPersonCount();
        $data->setTotalPrice($price);
        return $this->persist($data);
    }

    /**
     * @param array $criteria
     *
     * @return object|null|Booking
     */
    public function findOneBy($criteria = [])
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @return Booking[]
     */
    public function findAll()
    {
        $qb = $this->repository->findAll();

        return $qb;
    }

    /**
     * @return Booking
     */
    public function create()
    {
        return new Booking();
    }

    /**
     * @param Booking $booking
     * @return mixed
     */
    public function persist(Booking $booking)
    {
        $this->em->persist($booking);
        $this->em->flush();

        return $booking;
    }

    /**
     * @param Booking $booking
     *
     * @return string
     */
    public function remove(Booking $booking)
    {
        $this->em->remove($booking);
        $this->em->flush();

        return true;
    }

    /**
     * @param Booking $booking
     * @param User $user
     *
     * @return string
     */
    public function reject(Booking $booking, User $user)
    {
        switch ($user->getRole()){
            case "ROLE_USER":
                $booking->setTouristStatus(Booking::REJECTED);
                break;
            case "ROLE_EXPERT":
                $booking->setExpertStatus(Booking::REJECTED);
        }

        return $this->persist($booking);
    }

    /**
     * @param Booking $booking
     * @return mixed
     */
    public function invite(Booking $booking)
    {
        /** @var \DateTime $date */
        $date = $booking->getStartDate();
        $time = $booking->getTime();
        $date->setTime($time->format('H'), $time->format('i'));
        $booking->setStartDate($date);
        $booking->setTouristStatus(Booking::PENDING);
        $booking->setExpertStatus(Booking::APPROVE);

        return $this->persist($booking);
    }
}
