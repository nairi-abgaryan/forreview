<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CityRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class City
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"full","default"})
     * @Serializer\Expose
     */
    private $id;

    /**
     * @var Country
     * @ORM\ManyToOne(targetEntity="App\Entity\Country")
     */
    private $country;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     * @Serializer\Groups({"full","default"})
     * @Serializer\Expose
     */
    private $placeId;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\CityLang",
     *     cascade={"persist", "remove"},
     *     mappedBy="city",
     *     fetch="EXTRA_LAZY",
     *     orphanRemoval=true
     * )
     */
    private $cityLang;

    /**
     * @var string
     * @Serializer\Accessor(getter="getName", setter="setName")
     * @Serializer\Groups({"full","default"})
     * @SWG\Property(ref="#definitions/name")
     * @Serializer\Expose
     */
    private $name;

    /**
     * City constructor.
     */
    public function __construct()
    {
        $this->cityLang = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
     * @return mixed
     */
    public function getCityLang()
    {
        return $this->cityLang;
    }

    /**
     * @param mixed $cityLang
     */
    public function setCityLang($cityLang): void
    {
        $this->cityLang = $cityLang;
    }

    /**
     * @param $cityLang
     * @return bool
     */
    public function addCityLang($cityLang)
    {
        return $this->cityLang->add($cityLang);
    }

    /**
     * @return string
     */
    public function getName()
    {
        if ($this->cityLang->get(0)) {
            return $this->cityLang->get(0)->getName();
        }

        return null;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}

