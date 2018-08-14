<?php

namespace App\Form\Type;

use App\Entity\PreferenceSet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class PreferenceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("preferenceLang", CollectionType::class, [
                'entry_type' => PreferenceLangType::class,
                'by_reference' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => true,
            ])
            ->add("preferenceSet", EntityType::class, [
                "constraints" => [
                    new Assert\NotBlank()
                ],
                "class" => PreferenceSet::class
            ])
            ->add("images", CollectionType::class, [
                'entry_type' => ImageType::class,
                'by_reference' => true,
                'allow_add' => true,
                'required' => false
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Preference',
            'csrf_protection' => false
        ]);
    }
}
