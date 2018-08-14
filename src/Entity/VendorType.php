<?php

namespace App\Entity;

use App\Service\StatusService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VendorTypeRepository")
 * @ORM\Entity
 * @ORM\Table(name="vendor_type")
 * @Serializer\ExclusionPolicy("all")
 */
class VendorType
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"full","default"})
     * @Serializer\Expose
     */
    private $id;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\VendorTypeLang",
     *     cascade={"persist"},
     *     mappedBy="vendorType"
     * )
     * @Groups({"default"})
     * @Assert\Valid
     */
    private $vendorTypeLang;

    /**
     * @var array
     * @ORM\Column(type="json")
     * @Groups({"default"})
     * @Serializer\Expose
     */
    private $translates;

    /**
     * @var Image
     * @ORM\ManyToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove", "refresh"})
     * @ORM\JoinColumn(name="avatar_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * @Groups({"default"})
     * @Serializer\Expose
     */
    private $avatar;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     */
    private $appStatus;

    /**
     * @var string
     * @Serializer\Accessor(getter="getName", setter="setName")
     * @Serializer\Expose
     * @Serializer\Groups({"default", "full"})
     */
    private $name;

    /**
     * VendorType constructor.
     */
    public function __construct()
    {
        $this->appStatus = StatusService::UNPUBLISHED;
        $this->vendorTypeLang = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return mixed
     */
    public function getVendorTypeLang()
    {
        return $this->vendorTypeLang;
    }

    /**
     * @param VendorType $data
     * @param $lang
     */
    public function addVendorTypeLang(VendorType $data, $lang)
    {
        /** @var VendorTypeLang $vendorTypeLang*/
        foreach ($data->getVendorTypeLang() as $vendorTypeLang)
        {
            $vendorTypeLang->setVendorType($this);
            $vendorTypeLang->setLang($lang);
        }
    }

    /**
     * @param mixed $vendorTypeLang
     */
    public function setVendorTypeLang($vendorTypeLang): void
    {
        $this->vendorTypeLang = $vendorTypeLang;
    }

    /**
     * @return array
     */
    public function getTranslates(): array
    {
        return $this->translates;
    }

    /**
     * @return int
     */
    public function getAppStatus(): int
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
     * @param array $translates
     */
    public function setTranslates(array $translates): void
    {
        $this->translates = $translates;
    }

    /**
     * @return Image
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param Image $avatar
     */
    public function setAvatar(Image $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function __toString()
    {
        return sprintf('%s', $this->id);
    }

    /**
     * @return string
     */
    public function getName()
    {
        if ($this->vendorTypeLang->get(0)) {
            return $this->vendorTypeLang->get(0)->getName();
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

