<?php

namespace App\Form\Type;

use App\Entity\Preference;
use App\Entity\Vendor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class VendorPreferenceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('vendor', EntityType::class,[
                "class" => Vendor::class,
                "constraints" => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('preferences', EntityType::class,[
                "class" => Preference::class,
                "allow_extra_fields" => true,
                "multiple" => true,
                'expanded' => true
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}
