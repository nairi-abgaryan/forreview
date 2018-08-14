<?php

namespace App\Entity;

use App\Resource\Constant;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BaseTourRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class BaseTour
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"default", "full"})
     * @Serializer\Expose
     */
    private $id;

    /**
     * @Groups({"default"})
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\BaseTourLang",
     *     cascade={"persist", "remove"},
     *     mappedBy="baseTour",
     *     fetch="EXTRA_LAZY",
     *     orphanRemoval=true
     * )
     * @Assert\Valid
     */
    private $baseTourLang;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(
     *     targetEntity="App\Entity\Preference"
     * )
     * @Serializer\Groups({"default", "full"})
     * @Serializer\Expose
     */
    private $preferences;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(
     *     targetEntity="App\Entity\VendorType"
     * )
     * @Serializer\Groups({"default", "full"})
     * @Serializer\Expose
     */
    private $vendorType;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(
     *     targetEntity="Activity"
     * )
     * @Serializer\SerializedName("activity")
     * @Serializer\Groups({"default", "full"})
     * @Serializer\Expose
     */
    private $activity;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(
     *     targetEntity="App\Entity\Image",
     *     cascade={"persist", "remove", "refresh"}
     *     )
     * @Serializer\Groups({"default", "full"})
     * @Serializer\Expose
     */
    private $images;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\Entity\Vendor", cascade={"persist", "remove", "refresh"})
     * @Serializer\Groups({"default", "full"})
     * @Serializer\Expose
     */
    private $vendors;

    /**
     * @var array
     * @ORM\Column(type="json")
     * @Serializer\Groups({"default", "full"})
     * @Serializer\Expose
     */
    private $translates;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", onDelete="SET NULL")
     * @Serializer\Groups({"default", "full"})
     * @Serializer\Expose
     */
    private $city;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Country"
     * )
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id", onDelete="SET NULL")
     * @Serializer\Expose
     * @Serializer\Groups({"default", "full"})
     */
    private $country;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     * @Serializer\Groups({"default", "full"})
     */
    private $appStatus;

    /**
     * @var string
     * @Serializer\Accessor(getter="getTitle", setter="setTitle")
     * @Serializer\Expose
     * @Serializer\Groups({"default", "full"})
     * @SWG\Property(ref="#definitions/title")
     */
    private $title;

    /**
     * @var string
     * @Serializer\Accessor(getter="getDescription", setter="setDescription")
     * @Serializer\Expose
     * @Serializer\Groups({"default", "full"})
     * @SWG\Property(ref="#definitions/description")
     */
    private $description;

    /**
     * @var string
     */
    private $placeId;

    /**
     * BaseTour constructor.
     */
    public function __construct()
    {
        $this->appStatus = Constant::PUBLISHED;
        $this->images = new ArrayCollection();
        $this->baseTourLang= new ArrayCollection();
        $this->vendors= new ArrayCollection();
        $this->activity= new ArrayCollection();
        $this->vendorType= new ArrayCollection();
        $this->preferences= new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getBaseTourLang()
    {
        return $this->baseTourLang;
    }

    /**
     * @param mixed $baseTourLang
     */
    public function setBaseTourLang($baseTourLang): void
    {
        $this->baseTourLang = $baseTourLang;
    }

    /**
     * @return ArrayCollection
     */
    public function getPreferences()
    {
        return $this->preferences;
    }

    /**
     * @param ArrayCollection $preferences
     */
    public function setPreferences(ArrayCollection $preferences): void
    {
        $this->preferences = $preferences;
    }

    /**
     * @return ArrayCollection
     */
    public function getVendorType()
    {
        return $this->vendorType;
    }

    /**
     * @param ArrayCollection $vendorType
     */
    public function setVendorType(ArrayCollection $vendorType): void
    {
        $this->vendorType = $vendorType;
    }

    /**
     * @return ArrayCollection
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param ArrayCollection $activity
     */
    public function setActivity(ArrayCollection $activity): void
    {
        $this->activity = $activity;
    }

    /**
     * @return ArrayCollection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param ArrayCollection $images
     */
    public function setImages(ArrayCollection $images): void
    {
        $this->images = $images;
    }

    /**
     * @return ArrayCollection
     */
    public function getVendors()
    {
        return $this->vendors;
    }

    /**
     * @param ArrayCollection $vendors
     */
    public function setVendors(ArrayCollection $vendors): void
    {
        $this->vendors = $vendors;
    }


    /**
     * @return array
     */
    public function getTranslates()
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
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country): void
    {
        $this->country = $country;
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
     * @param Image $image
     */
    public function addImages($image)
    {
        $this->images->add($image);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        if ($this->baseTourLang->get(0)) {
            return $this->baseTourLang->get("0")->getTitle();
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
    public function getDescription()
    {
        if ($this->baseTourLang->get(0)) {
            return $this->baseTourLang->get(0)->getDescription();
        }

        return null;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getPlaceId()
    {
        return $this->placeId;
    }

    /**
     * @param string $placeId
     */
    public function setPlaceId(string $placeId): void
    {
        $this->placeId = $placeId;
    }

    /**
     * @param BaseTour $data
     * @param $lang
     */
    public function addBaseTourLang($data, $lang)
    {
        /** @var BaseTourLang $baseTourLang */
        foreach ($data->getBaseTourLang() as $baseTourLang)
        {
            $baseTourLang->setBaseTour($this);
            $baseTourLang->setLang($lang);
        }
    }
}
