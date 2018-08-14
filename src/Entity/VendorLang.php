<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VendorLangRepository")
 * @ORM\Entity
 * @ORM\Table(name="vendor_lang")
 * @Serializer\ExclusionPolicy("all")
 * @UniqueEntity(
 *     fields={"title"},
 *     errorPath="title",
 *     message="This title is already in use."
 * )
 */
class VendorLang
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Vendor
     * @ORM\ManyToOne(targetEntity="App\Entity\Vendor", inversedBy="vendorLang", cascade={"persist"})
     * @ORM\JoinColumn(name="vendor_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $vendor;

    /**
     * @var string
     * @ORM\Column(type="string", unique=True)
     * @Serializer\Expose
     * @Groups({"default"})
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\Expose
     * @Groups({"default"})
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"default"})
     * @Serializer\Expose
     */
    private $address;

    /**
     * @var string
     * @ORM\Column(type="string", length=2)
     * @Serializer\Expose
     */
    private $lang;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Vendor
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @param Vendor $vendor
     */
    public function setVendor(Vendor $vendor): void
    {
        $this->vendor = $vendor;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address): void
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param string $lang
     */
    public function setLang(string $lang): void
    {
        $this->lang = $lang;
    }

    public function __toString()
    {
        return sprintf('%s', $this->title);
    }
}
