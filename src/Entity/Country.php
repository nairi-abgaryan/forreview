<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CountryRepository")
 * @ORM\Entity
 * @ORM\Table(name="countries")
 * @Serializer\ExclusionPolicy("all")
 */
class Country
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"default", "full"})
     *
     * @Serializer\Expose
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=3)
     * @Groups({"default"})
     *
     * @Serializer\Expose
     */
    private $iso;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\CountryLang",
     *     mappedBy="country",
     *     cascade={"persist", "remove"},
     *     mappedBy="country",
     *     fetch="EXTRA_LAZY",
     *     orphanRemoval=true
     * )
     */
    private $countryLang;

    /**
     * @var string
     * @Serializer\Accessor(getter="getName", setter="setName")
     * @Serializer\Expose
     * @Serializer\Groups({"default", "full"})
     * @Serializer\Expose
     * @SWG\Property(ref="#definitions/name")
     */
    private $name;

    /**
     * Country constructor.
     */
    public function __construct()
    {
        $this->countryLang = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIso()
    {
        return $this->iso;
    }


    /**
     * @param string $iso
     */
    public function setIso(string $iso): void
    {
        $this->iso = $iso;
    }

    /**
     * @return mixed
     */
    public function getCountryLang()
    {
        return $this->countryLang;
    }

    /**
     * @param mixed $countryLang
     */
    public function setCountryLang($countryLang): void
    {
        $this->countryLang = $countryLang;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if ($this->countryLang->get(0)) {
            return $this->countryLang->get(0)->getName();
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

    public function __toString()
    {
        return sprintf('%s', $this->iso);
    }
}
