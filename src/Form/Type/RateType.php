<?php

namespace App\Form\Type;

use App\Entity\Rate;
use App\Entity\Tour;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RateType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder
             ->add("tour", EntityType::class, [
                 "class" => Tour::class,
                 "by_reference" => true,
                 "required" => true
             ])
             ->add("value", IntegerType::class, [
                 "constraints" => [
                     new Assert\NotBlank(),
                     new Assert\Range([
                         "min" => 0, "max" => 5
                         ])
                 ]
             ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Rate::class,
            "csrf_protection" => false
        ]);
    }
}
