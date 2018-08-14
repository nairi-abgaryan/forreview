<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TourLangRepository")
 */
class TourLang
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Tour
     * @ORM\ManyToOne(targetEntity="App\Entity\Tour", inversedBy="tourLang", cascade={"persist"})
     */
    private $tour;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $agenda;

    /**
     * @ORM\Column(type="string")
     */
    private $uniqueAdvantages;

    /**
     * @var string
     * @ORM\Column(type="string", length=2)
     */
    private $lang;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getAgenda()
    {
        return $this->agenda;
    }

    /**
     * @param mixed $agenda
     */
    public function setAgenda($agenda): void
    {
        $this->agenda = $agenda;
    }

    /**
     * @return mixed
     */
    public function getUniqueAdvantages()
    {
        return $this->uniqueAdvantages;
    }

    /**
     * @param mixed $uniqueAdvantages
     */
    public function setUniqueAdvantages($uniqueAdvantages): void
    {
        $this->uniqueAdvantages = $uniqueAdvantages;
    }

    /**
     * @return Tour
     */
    public function getTour(): Tour
    {
        return $this->tour;
    }

    /**
     * @param Tour $tour
     */
    public function setTour(Tour $tour): void
    {
        $this->tour = $tour;
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

