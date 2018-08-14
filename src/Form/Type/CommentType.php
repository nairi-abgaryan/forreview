<?php

namespace App\Form\Type;

use App\Entity\Comment;
use App\Entity\Tour;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CommentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder
             ->add("tour", EntityType::class, [
                 "class" => Tour::class,
                 "by_reference" => true,
                 "required" => true
             ])
             ->add("comment", TextType::class, [
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
            "data_class" => Comment::class,
            "csrf_protection" => false
        ]);
    }
}
