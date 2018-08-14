<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BaseTourRepository")
 * @ORM\Entity
 * @UniqueEntity(
 *     fields={"title"},
 *     errorPath="title",
 *     message="This title is already in use."
 * )
 */
class BaseTourLang
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var BaseTour
     * @ORM\ManyToOne(targetEntity="App\Entity\BaseTour", inversedBy="baseTourLang")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=true)
     */
    private $baseTour;

    /**
     * @var string
     * @ORM\Column(type="string", unique=True)
     * @Groups({"default"})
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Groups({"default"})
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="string", length=2)
     */
    private $lang;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return BaseTour
     */
    public function getBaseTour()
    {
        return $this->baseTour;
    }

    /**
     * @param BaseTour $baseTour
     */
    public function setBaseTour(BaseTour $baseTour): void
    {
        $this->baseTour = $baseTour;
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
    public function setTitle(string $title): void
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
    public function setDescription(string $description): void
    {
        $this->description = $description;
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
}

