<?php

namespace App\Form\Type;

use App\Entity\UserLang;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserLangType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("firstName", TextType::class, [
                "constraints" => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add("lastName", TextType::class, [
                "constraints" => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add("bio", TextType::class, [
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
            "data_class" => UserLang::class,
            "csrf_protection" => false,
        ]);
    }
}
