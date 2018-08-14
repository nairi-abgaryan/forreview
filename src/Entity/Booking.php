<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 */
class Booking
{
    CONST REJECTED = 0;
    CONST APPROVE = 1;
    CONST ENQUIRIES = 2;
    CONST PENDING = 4;
    CONST STRIPE_CANCELED = 3;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"full", "default", "trips", "my_experiences"})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     * @Serializer\Groups({"full", "default"})
     */
    private $code;

    /**
     * @var Image
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Image",
     *     cascade={"persist"}
     * )
     * @Serializer\Groups({"full", "default"})
     */
    private $qrCode;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @Serializer\Groups({"full", "default"})
     */
    private $owner;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $expert;

    /**
     * @var Tour
     * @ORM\ManyToOne(targetEntity="App\Entity\Tour")
     * @Serializer\Groups({"full", "default", "trips", "my_experiences"})
     */
    private $tour;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Type("DateTime<'Y-m-d h:m:s'>")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "default", "trips", "my_experiences"})
     */
    private $startDate;

    /**
     * @var string
     */
    private $time;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "default"})
     */
    private $personCount;

    /**
     * @var float
     * @ORM\Column(type="float")
     * @Serializer\Groups({"full", "default", "trips", "my_experiences"})
     */
    private $totalPrice;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Groups({"full", "default"})
     */
    private $currency;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"full", "default"})
     */
    private $touristStatus;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"full", "default"})
     */
    private $expertStatus;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Serializer\Groups({"full", "default"})
     */
    private $startTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Serializer\Groups({"full", "default"})
     */
    private $endTime;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", options={"default" = 0})
     * @Serializer\Groups({"full", "default", "trips", "my_experiences"})
     * @Serializer\Expose
     */
    private $isRate = false;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     */
    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @return Tour
     */
    public function getTour()
    {
        return $this->tour;
    }

    /**
     * @param Tour $tour
     */
    public function setTour(Tour $tour): void
    {
        $this->tour = $tour;
    }

    /**
     * @return User
     */
    public function getExpert()
    {
        return $this->expert;
    }

    /**
     * @param User $expert
     */
    public function setExpert(User $expert): void
    {
        $this->expert = $expert;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time): void
    {
        $this->time = $time;
    }

    /**
     * @return int
     */
    public function getPersonCount()
    {
        return $this->personCount;
    }

    /**
     * @param int $personCount
     */
    public function setPersonCount(int $personCount): void
    {
        $this->personCount = $personCount;
    }

    /**
     * @return float
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * @param float $totalPrice
     */
    public function setTotalPrice(float $totalPrice): void
    {
        $this->totalPrice = $totalPrice;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getTouristStatus()
    {
        return $this->touristStatus;
    }

    /**
     * @param mixed $touristStatus
     */
    public function setTouristStatus($touristStatus): void
    {
        $this->touristStatus = $touristStatus;
    }

    /**
     * @return mixed
     */
    public function getExpertStatus()
    {
        return $this->expertStatus;
    }

    /**
     * @param mixed $expertStatus
     */
    public function setExpertStatus($expertStatus): void
    {
        $this->expertStatus = $expertStatus;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param mixed $startTime
     */
    public function setStartTime($startTime): void
    {
        $this->startTime = $startTime;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param mixed $endTime
     */
    public function setEndTime($endTime): void
    {
        $this->endTime = $endTime;
    }

    /**
     * @return Image
     */
    public function getQrCode()
    {
        return $this->qrCode;
    }

    /**
     * @param Image $qrCode
     */
    public function setQrCode(Image $qrCode): void
    {
        $this->qrCode = $qrCode;
    }

    /**
     * @return bool
     */
    public function isRate()
    {
        return $this->isRate;
    }

    /**
     * @param bool $isRate
     */
    public function setIsRate(bool $isRate): void
    {
        $this->isRate = $isRate;
    }
}

