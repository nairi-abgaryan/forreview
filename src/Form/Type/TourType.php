<?php

namespace App\Form\Type;

use App\Entity\BaseTour;
use App\Entity\Discount;
use App\Entity\Duration;
use App\Entity\Time;
use App\Entity\Tour;
use App\Entity\Vendor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TourType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("time", EntityType::class, [
                'by_reference' => true,
                "class" => Time::class,
                "multiple" => false
            ])
            ->add("duration", EntityType::class, [
                'by_reference' => true,
                "class" => Duration::class,
                "multiple" => false
            ])
            ->add("images", CollectionType::class, [
                'entry_type' => ImageType::class,
                'by_reference' => true,
                'allow_add' => true,
                'required' => false
            ])
            ->add('price', TextType::class)
            ->add("percentDiscount", EntityType::class, [
                'by_reference' => true,
                "class" => Discount::class,
                "multiple" => false
            ])
            ->add("vendor", EntityType::class, [
                'by_reference' => true,
                "class" => Vendor::class,
                "multiple" => true
            ])
            ->add("baseTour", EntityType::class, [
                'by_reference' => true,
                "class" => BaseTour::class,
                "multiple" => false
            ])
            ->add("tourLang", CollectionType::class, [
                'entry_type' => TourLangType::class,
                'by_reference' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "allow_extra_fields" => true,
            "data_class" => Tour::class,
            'csrf_protection' => false
        ]);
    }
}
