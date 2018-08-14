<?php

namespace App\Form\Type;

use App\Entity\Activity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder
             ->add('activityLang', CollectionType::class, [
                 'entry_type' => ActivityLangType::class,
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
            "data_class" => Activity::class,
            'csrf_protection' => false
        ]);
    }
}
