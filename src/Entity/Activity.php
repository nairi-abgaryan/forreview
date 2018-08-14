<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActivityRepository")
 * @ORM\Entity
 * @ORM\Table(name="activity")
 */
class Activity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"default", "full"})
     */
    private $id;

    /**
     * @ORM\OneToMany(
     *     targetEntity="ActivityLang",
     *     cascade={"persist"},
     *     mappedBy="activity"
     * )
     * @Assert\Valid()
     */
    private $activityLang;

    /**
     * @var Image
     * @ORM\ManyToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove", "refresh"})
     * @ORM\JoinColumn(name="avatar_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * @Serializer\Groups({"default", "full"})
     */
    private $avatar;

    /**
     * @var array
     * @ORM\Column(type="json")
     * @Serializer\Groups({"default", "full"})
     * @SWG\Property(ref="#definitions/translation")
     */
    private $translates;

    /**
     * @var string
     * @Serializer\Accessor(getter="getName", setter="setName")
     * @Serializer\Expose
     * @Serializer\Groups({"default", "full"})
     * @SWG\Property(ref="#definitions/name")
     */
    private $name;

    /**
     * Activity constructor.
     */
    public function __construct()
    {
        $this->activityLang = new ArrayCollection();
    }

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
     * @return mixed
     */
    public function getActivityLang()
    {
        return $this->activityLang;
    }

    /**
     * @param mixed $activityLang
     */
    public function setActivityLang($activityLang): void
    {
        $this->activityLang = $activityLang;
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

    /**
     * @return string
     */
    public function getName()
    {
        if ($this->activityLang->get(0)) {
            return $this->activityLang->get(0)->getName();
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

    /**
     * @param Activity $data
     * @param $lang
     */
    public function addActivityLang($data, $lang)
    {
        /** @var ActivityLang $activityLang */
        foreach ($data->getActivityLang() as $activityLang)
        {
            $activityLang->setActivity($this);
            $activityLang->setLang($lang);
        }
    }
}

