<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VendorTypeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('vendorTypeLang', CollectionType::class, [
                'entry_type' => VendorTypeLangType::class,
                'by_reference' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => true,
            ])
            ->add("avatar", ImageType::class, [
                'by_reference' => true
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => \App\Entity\VendorType::class,
            'csrf_protection' => false
        ]);
    }
}
