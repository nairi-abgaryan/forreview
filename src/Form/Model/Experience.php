<?php

namespace App\Form\Model;
use JMS\Serializer\Annotation as Serializer;

class Experience
{
    /**
     * @var
     * @Serializer\Expose
     * @Serializer\Groups({"my_experiences"})
     */
    public $id;

    /**
     * @var
     * @Serializer\Expose
     * @Serializer\Groups({"my_experiences"})
     */
    public $tour;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getTour()
    {
        return $this->tour;
    }

    /**
     * @param  $tour
     */
    public function setTour($tour): void
    {
        $this->tour = $tour;
    }
}
