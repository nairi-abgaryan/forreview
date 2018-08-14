<?php

namespace App\Form\Type;

use App\Entity\TourLang;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class TourLangType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder
             ->add('title', TextType::class, [
                 "constraints" => [
                    new Assert\NotBlank()
                 ]
             ])
             ->add('agenda', TextType::class, [
                 "constraints" => [
                     new Assert\NotBlank()
                 ]
             ])
             ->add('uniqueAdvantages', TextType::class, [
                 "constraints" => [
                     new Assert\NotBlank()
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
            "data_class" => TourLang::class,
            'csrf_protection' => false
        ]);
    }
}
