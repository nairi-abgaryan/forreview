<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VendorTypeRepository")
 * @ORM\Entity
 * @UniqueEntity(
 *     fields={"name"},
 *     errorPath="name",
 *     message="This name is already in use."
 * )
 */
class VendorTypeLang
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var VendorType
     * @ORM\ManyToOne(targetEntity="App\Entity\VendorType")
     * @ORM\JoinColumn(name="vendor_type_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $vendorType;

    /**
     * @var string
     * @ORM\Column(type="string", unique=True)
     * @Groups({"default"})
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=2)
     */
    private $lang;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
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

    /**
     * @return VendorType
     */
    public function getVendorType()
    {
        return $this->vendorType;
    }

    /**
     * @param VendorType $vendorType
     */
    public function setVendorType(VendorType $vendorType): void
    {
        $this->vendorType = $vendorType;
    }
}
