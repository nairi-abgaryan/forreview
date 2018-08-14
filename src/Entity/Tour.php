<?php

namespace App\Entity;

use App\Resource\Constant;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TourRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Tour
{
    use TimestampableEntity;
    CONST FINISHED = 5;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"trips", "my_experiences"})
     * @Serializer\Expose
     */
    private $id;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Time"
     * )
     * @Serializer\Expose
     * @Serializer\Groups({"trips", "my_experiences"})
     */
    private $time;

    /**
     * @var User
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\User"
     * )
     * @Serializer\Expose
     * @Serializer\Groups({"trips", "my_experiences"})
     */
    private $owner;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Duration"
     * )
     * @Serializer\Groups({"trips", "my_experiences"})
     * @Serializer\Expose
     */
    private $duration;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(
     *     targetEntity="App\Entity\Image",
     *     fetch="EXTRA_LAZY",
     *     cascade={"persist"}
     *     )
     * @Serializer\Groups({"trips", "my_experiences"})
     * @Serializer\Expose
     */
    private $images;

    /**
     * @ORM\Column(type="float")
     * @Serializer\Groups({"trips", "my_experiences"})
     * @Serializer\Expose
     */
    private $price;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Discount"
     * )
     * @Serializer\Groups({"trips", "my_experiences"})
     * @Serializer\Expose
     */
    private $percentDiscount;

    /**
     * @ORM\ManyToMany(
     *     targetEntity="App\Entity\Vendor"
     * )
     * @Serializer\Expose
     */
    private $vendor;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\BaseTour"
     * )
     * @Serializer\Expose
     */
    private $baseTour;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"trips", "my_experiences"})
     * @Serializer\Expose
     */
    private $status;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     * @Serializer\Groups({"default", "full"})
     */
    private $appStatus;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\TourLang",
     *     mappedBy="tour",
     *     fetch="EXTRA_LAZY",
     *     cascade={"persist"}
     * )
     */
    private $tourLang;

    /**
     * @var array
     * @ORM\Column(type="json")
     * @Serializer\Expose
     */
    private $translates;

    /**
     * @var string
     * @Serializer\Accessor(getter="getTitle", setter="setTitle")
     * @Serializer\Groups({"default", "full", "trips", "my_experiences"})
     * @Serializer\Expose
     */
    private $title;

    /**
     * @var string
     * @Serializer\Accessor(getter="getAgenda", setter="setAgenda")
     * @Serializer\Expose
     * @Serializer\Groups({"default", "full", "trips", "my_experiences"})
     */
    private $agenda;

    /**
     * @var string
     * @Serializer\Accessor(getter="getUniqueAdvantages", setter="setUniqueAdvantages")
     * @Serializer\Expose
     * @Serializer\Groups({"default", "full", "trips", "my_experiences"})
     */
    private $uniqueAdvantages;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\Rate",
     *     mappedBy="tour",
     *     fetch="EXTRA_LAZY",
     *     cascade={"persist"}
     * )
     */
    private $rates;

    /**
     * @Serializer\Accessor(getter="getRate")
     * @Serializer\Expose
     * @Serializer\Groups({"default", "full", "trips", "my_experiences"})
     */
    private $rate;

    /**
     * Expert constructor.
     */
    public function __construct()
    {
        $this->status = Constant::NOT_APPROVED;
        $this->appStatus = Constant::UNPUBLISHED;
        $this->images = new ArrayCollection();
        $this->tourLang = new ArrayCollection();
        $this->vendor = new ArrayCollection();
        $this->rates = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
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
     * @return mixed
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param mixed $images
     */
    public function setImages($images): void
    {
        $this->images = $images;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getPercentDiscount()
    {
        return $this->percentDiscount;
    }

    /**
     * @param mixed $percentDiscount
     */
    public function setPercentDiscount($percentDiscount): void
    {
        $this->percentDiscount = $percentDiscount;
    }

    /**
     * @return mixed
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @param mixed $vendor
     */
    public function setVendor($vendor): void
    {
        $this->vendor = $vendor;
    }

    /**
     * @return mixed
     */
    public function getBaseTour()
    {
        return $this->baseTour;
    }

    /**
     * @param mixed $baseTour
     */
    public function setBaseTour($baseTour): void
    {
        $this->baseTour = $baseTour;
    }

    /**
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param mixed $duration
     */
    public function setDuration($duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getTourLang()
    {
        return $this->tourLang;
    }

    /**
     * @param mixed $tourLang
     */
    public function setTourLang($tourLang): void
    {
        $this->tourLang = $tourLang;
    }

    /**
     * @return array
     */
    public function getTranslates(): array
    {
        return $this->translates;
    }

    /**
     * @param array $translates
     */
    public function setTranslates(array $translates): void
    {
        $this->translates = $translates;
    }

    /**
     * @return int
     */
    public function getAppStatus()
    {
        return $this->appStatus;
    }

    /**
     * @param int $appStatus
     */
    public function setAppStatus(int $appStatus): void
    {
        $this->appStatus = $appStatus;
    }

    /**
     * @param Tour $data
     * @param $lang
     */
    public function addTourLang($data, $lang)
    {
        /** @var TourLang $tourLang */
        foreach ($data->getTourLang() as $tourLang)
        {
            $tourLang->setTour($this);
            $tourLang->setLang($lang);
        }
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        if ($this->tourLang->get(0)) {
            return $this->tourLang->get(0)->getTitle();
        }

        return null;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getAgenda()
    {
        if ($this->tourLang->get(0)) {
            return $this->tourLang->get(0)->getAgenda();
        }

        return null;
    }

    /**
     * @param string $agenda
     */
    public function setAgenda(string $agenda): void
    {
        $this->agenda = $agenda;
    }

    /**
     * @return string
     */
    public function getUniqueAdvantages()
    {
        if ($this->tourLang->get(0)) {
            return $this->tourLang->get(0)->getUniqueAdvantages();
        }

        return null;
    }

    /**
     * @param string $uniqueAdvantages
     */
    public function setUniqueAdvantages(string $uniqueAdvantages): void
    {
        $this->uniqueAdvantages = $uniqueAdvantages;
    }

    /**
     * @param Image $image
     */
    public function addImages($image)
    {
        $this->images->add($image);
    }

    /**
     * @return mixed
     */
    public function getRate()
    {
        $rate = 0;

        if ($this->rates->count() != 0){
            /** @var Rate $value */
            foreach ($this->rates->getValues() as $value){
                $rate+= $value->getValue();
            }

            $rate = $rate/$this->rates->count();
        }

        return $rate;
    }
}

