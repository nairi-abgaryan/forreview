<?php

namespace App\Entity;

use App\Resource\Constant;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VendorRepository")
 * @ORM\Entity
 * @ORM\Table(name="vendor")
 * @Serializer\ExclusionPolicy("all")
 */
class Vendor
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"full", "default"})
     * @Serializer\Expose
     */
    private $id;

    /**
     * @var string
     * @Serializer\Groups({"full"})
     * @ORM\Column(type="string", nullable=true)
     * @Serializer\Groups({"default"})
     * @Serializer\Expose
     */
    private $website;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     * @Serializer\Groups({"default"})
     * @Serializer\Expose
     */
    private $facebook;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     * @Serializer\Groups({"default"})
     * @Serializer\Expose
     */
    private $phone;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"full", "default"})
     * @Serializer\Expose
     */
    private $status;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"full", "default"})
     * @Serializer\Expose
     */
    private $appStatus;

    /**
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\VendorLang",
     *     mappedBy="vendor",
     *     fetch="EXTRA_LAZY",
     *     cascade={"persist"}
     * )
     * @Assert\Valid
     */
    private $vendorLang;

    /**
     * @var array
     * @ORM\Column(type="json")
     * @Serializer\Groups({"full", "default"})
     * @Serializer\Expose
     */
    private $translates;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(
     *     targetEntity="Activity"
     * )
     * @Serializer\Expose
     */
    private $activity;

    /**
     * @ORM\ManyToMany(
     *     targetEntity="App\Entity\VendorType"
     * )
     * @Serializer\Groups({"full","default"})
     * @Serializer\Expose
     */
    private $vendorType;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(
     *     targetEntity="App\Entity\Image",
     *     fetch="EXTRA_LAZY",
     *     cascade={"persist"}
     *     )
     * @Serializer\Groups({"full","default"})
     * @Serializer\Expose
     */
    private $images;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(
     *     targetEntity="App\Entity\Preference",
     *     fetch="EXTRA_LAZY"
     * )
     * @Serializer\Expose
     */
    private $preferences;

    /**
     * @var City
     * @ORM\ManyToOne(targetEntity="App\Entity\City")
     * @Serializer\Groups({"full","default"})
     * @Serializer\Expose
     */
    private $city;

    /**
     * @var Country
     * @ORM\ManyToOne(targetEntity="App\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id", onDelete="SET NULL")
     * @Serializer\Groups({"full","default"})
     * @Serializer\Expose
     */
    private $country;

    /**
     * @var string
     * @Serializer\Accessor(getter="getTitle", setter="setTitle")
     * @Serializer\Expose
     * @Serializer\Groups({"full","default"})
     */
    private $title;

    /**
     * @var string
     * @Serializer\Accessor(getter="getDescription", setter="setDescription")
     * @Serializer\Expose
     * @Serializer\Groups({"full","default"})
     */
    private $description;

    /**
     * @var string
     * @Serializer\Accessor(getter="getAddress", setter="setAddress")
     * @Serializer\Groups({"full","default"})
     * @Serializer\Expose
     */
    private $address;

    /**
     * @var string
     */
    private $placeId;

    /**
     * Vendor constructor.
     */
    public function __construct()
    {
        $this->status = Constant::NOT_APPROVED;
        $this->appStatus = Constant::UNPUBLISHED;
        $this->vendorLang = new ArrayCollection();
        $this->preferences = new ArrayCollection();
        $this->activity = new ArrayCollection();
        $this->vendorType = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     */
    public function setWebsite($website): void
    {
        $this->website = $website;
    }

    /**
     * @return string
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * @param  $facebook
     */
    public function setFacebook($facebook): void
    {
        $this->facebook = $facebook;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param  $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return integer
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param integer $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getVendorLang()
    {
        return $this->vendorLang;
    }

    /**
     * @param mixed $vendorLang
     */
    public function setVendorLang($vendorLang): void
    {
        $this->vendorLang = $vendorLang;
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
     * @return mixed
     */
    public function getVendorType()
    {
        return $this->vendorType;
    }

    /**
     * @param mixed $vendorType
     */
    public function setVendorType($vendorType): void
    {
        $this->vendorType = $vendorType;
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
    public function getPreferences()
    {
        return $this->preferences;
    }

    /**
     * @param ArrayCollection $preferences
     */
    public function setPreferences($preferences): void
    {
        $this->preferences = $preferences;
    }

    /**
     * @param Vendor $data
     * @param $lang
     */
    public function addVendorLang($data, $lang)
    {
        /** @var VendorLang $vendorLang */
        foreach ($data->getVendorLang() as $vendorLang)
        {
            $vendorLang->setVendor($this);
            $vendorLang->setLang($lang);
        }
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param City $city
     */
    public function setCity(City $city): void
    {
        $this->city = $city;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param Country $country
     */
    public function setCountry(Country $country): void
    {
        $this->country = $country;
    }

    /**
     * @return integer
     */
    public function getAppStatus()
    {
        return $this->appStatus;
    }

    /**
     * @param mixed $appStatus
     */
    public function setAppStatus($appStatus): void
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
        if ($this->vendorLang->get(0)) {
            return $this->vendorLang->get(0)->getTitle();
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
        if ($this->vendorLang->get(0)) {
            return $this->vendorLang->get(0)->getDescription();
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
    public function getAddress()
    {
        if ($this->vendorLang->get(0)) {
            return $this->vendorLang->get(0)->getAddress();
        }

        return null;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
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

    public function __toString()
    {
        return sprintf('%s', $this->id);
    }
}

