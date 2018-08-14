<?php

namespace App\Form\Extension;

use App\Form\DataTransformer\FileToBase64Transformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Base64FileType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new FileToBase64Transformer($options['strict_decode']));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'compound' => false,
                'data_class' => null,
                'empty_data' => null,
                'multiple' => false,
                'strict_decode' => true,
            ])
            ->setAllowedTypes('strict_decode', 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        if ('form' === parent::getParent()) {
            return 'text';
        }

        return 'Symfony\Component\Form\Extension\Core\Type\TextType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'file_base64_encoded';
    }
}
