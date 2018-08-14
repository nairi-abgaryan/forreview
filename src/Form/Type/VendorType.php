<?php

namespace App\Form\Type;

use App\Entity\Country;
use App\Entity\Preference;
use App\Entity\Activity;
use App\Entity\Vendor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class VendorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('website', TextType::class)
            ->add('facebook', TextType::class)
            ->add('phone', TextType::class)
            ->add('status', TextType::class)
            ->add("vendorLang", CollectionType::class, [
                'entry_type' => VendorLangType::class,
                'by_reference' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => true,
            ])
            ->add("activity", EntityType::class, [
                'by_reference' => true,
                "class" => Activity::class,
                "multiple" => true
            ])
            ->add("vendorType", EntityType::class, [
                "constraints" => [
                    new Assert\Count(array(
                        'min' => 1,
                        'minMessage' => 'At least 1 choice is required',
                    )),
                    new Assert\NotBlank()
                ],
                'by_reference' => false,
                "class" => \App\Entity\VendorType::class,
                "multiple" => true,
                "required" => true
            ])
            ->add("preferences", EntityType::class, [
                'by_reference' => true,
                "class" => Preference::class,
                "multiple" => true
            ])
            ->add("images", CollectionType::class, [
                'entry_type' => ImageType::class,
                'by_reference' => true,
                'allow_add' => true,
                'required' => false
            ])
            ->add('country', EntityType::class,[
                "class" => Country::class,
                "constraints" => [
                    new Assert\NotBlank()
                ]
            ])
            ->add("place_id", TextType::class, [
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
            "data_class" => Vendor::class,
            'csrf_protection' => false
        ]);
    }
}
