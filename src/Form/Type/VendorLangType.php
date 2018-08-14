<?php

namespace App\Form\Type;

use App\Entity\VendorLang;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class VendorLangType extends AbstractType
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
            ->add('description', TextType::class, [
                "required" => false
            ])
            ->add('address', TextType::class, [
                "required" => false
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => VendorLang::class,
            'csrf_protection' => false,
        ]);
    }
}
