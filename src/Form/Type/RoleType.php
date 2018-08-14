<?php

namespace App\Form\Type;

use App\Entity\Role;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder
             ->add("role", EntityType::class, [
                 "by_reference" => true,
                 "class" => Role::class,
                 "query_builder" => function (EntityRepository $er) {
                     return $er->createQueryBuilder("r")
                         ->where("r.type =:type")
                         ->setParameter("type",true);
                 },
                 "multiple" => false
             ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false
        ]);
    }
}
