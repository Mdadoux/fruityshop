<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label'=> false,
                'attr'=>[
                    'placeholder'=>'Votre email',
                ]
            ])
            /*https://symfony.com/doc/current/reference/forms/types/password.html#hash-property-path*/
            ->add('plainPassword',RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 2,
                        'max' => 30,
                    ])
                ],
                'first_options' => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Choisissez votre mot de passe',
                    ],
                    'hash_property_path' => 'password',
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Confirmer votre mot de passe',
                    ]
                ],
                'mapped' => false,
            ])   ->add('firstname',TextType::class,[
                'label'=>false,
                'constraints' => [
                  new NotBlank(),
                  new Length([
                      'min' => 2,
                      'max' => 30,
                  ])
                ],
                'attr'=>[
                    'placeholder'=>'Nom',
                ]
            ])
            ->add('lastname',TextType::class,[
                'label'=>false,
                'constraints' =>[
                    new Length([
                        'min' => 2,
                        'max' => 30,
                    ])
                ],
                'attr'=>[
                    'placeholder'=>'Prénom'
                ]
            ])
            ->add('submit', SubmitType::class,[
                'label' => 'Valider inscription',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // resourdre l'application du unique entity
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
