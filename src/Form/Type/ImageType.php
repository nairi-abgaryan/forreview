<?php

namespace App\Form\Type;

use App\Entity\Image;
use App\Form\Extension\Base64FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ImageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image',Base64FileType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\File(['maxSize' => '10M']),
                    new Assert\Image(),
                ],
            ])
            ->add('position', IntegerType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
            'csrf_protection' => false
        ]);
    }
}
