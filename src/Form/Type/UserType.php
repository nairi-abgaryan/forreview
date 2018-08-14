<?php

namespace App\Form\Type;

use App\Entity\Country;
use App\Entity\Language;
use App\Entity\Preference;
use App\Entity\PreferenceTag;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("userLang", CollectionType::class, [
                'entry_type' => UserLangType::class,
                'by_reference' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
            ])
            ->add("email", TextType::class, [
                "constraints" => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ],
            ])
            ->add("plainPassword", TextType::class, [
                "constraints" => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        "pattern"=>"/^(?=.*[a-z])(?=.*\d).{6,}$/i",
                        "message" => "Password should have at least 8 characters, one number and letters"
                    ])
                ],
            ])
            ->add("phone", TextType::class, [
                "required" => false,
                'empty_data' => null
            ])
            ->add("avatar", ImageType::class, [
                'by_reference' => true,
                'required' => false
            ])
            ->add("role", EntityType::class, [
                "by_reference" => true,
                "class" => Role::class,
                "query_builder" => function (EntityRepository $er) {
                    return $er->createQueryBuilder("r")
                        ->where("r.type =:type")
                        ->setParameter("type",true);
                },
                "multiple" => false
            ])
            ->add("gender",  ChoiceType::class, array(
                'choices'  => [
                    'Male' => "Male",
                    'Female' => "Female",
                    'Other' => "Other"
                ],
                "required" => false
            ))
            ->add("spokenLanguages", EntityType::class, [
                "by_reference" => false,
                "class" => Language::class,
                "multiple" => true
            ])
            ->add("place_id", TextType::class, [
                "required" => false
            ])
            ->add('country', EntityType::class, [
                "class" => Country::class,
                "required" => false
            ])
            ->add("preferences", EntityType::class, [
                "by_reference" => false,
                "class" => Preference::class,
                "multiple" => true
            ])
            ->add("preferenceTag", EntityType::class, [
                "by_reference" => false,
                "class" => PreferenceTag::class,
                "multiple" => true
            ])
            ->add("dob", BirthdayType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy/MM/dd',
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
            "data_class" => User::class,
            "allow_extra_fields" => true,
            "csrf_protection" => false,
        ]);
    }
}
