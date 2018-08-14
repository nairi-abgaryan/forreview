<?php

namespace App\Form\Type;

use App\Entity\Country;
use App\Entity\Activity;
use App\Entity\Vendor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class BaseTourType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("baseTourLang", CollectionType::class, [
                'entry_type' => BaseTourLangType::class,
                'by_reference' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => true
            ])
            ->add("preferences", EntityType::class, [
                'class' => "App\Entity\Preference",
                "multiple" => true,
                'by_reference' => true
            ])
            ->add("activity", EntityType::class, [
                'by_reference' => true,
                "class" => Activity::class,
                "multiple" => true
            ])
            ->add("vendors", EntityType::class, [
                'by_reference' => true,
                "class" => Vendor::class,
                "multiple" => true,
                "required" => false
            ])
            ->add("vendorType", EntityType::class, [
                'by_reference' => true,
                "class" => \App\Entity\VendorType::class,
                "multiple" => true
            ])
            ->add("images", CollectionType::class, [
                'entry_type' => ImageType::class,
                'by_reference' => true,
                'allow_add' => true,
                'required' => false
            ])
            ->add("place_id", TextType::class, [
                "required" => false
            ])
            ->add('country', EntityType::class, [
                "class" => Country::class,
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
            'allow_extra_fields' => true,
            'data_class' => 'App\Entity\BaseTour',
            'csrf_protection' => false
        ]);
    }
}
