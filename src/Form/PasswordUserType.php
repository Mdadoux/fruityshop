<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('actualPassword', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Renseigner mot de passe actuel',
                ]
            ])
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
                    'label' => 'Nouveau mot de passe',
                    'attr' => [
                        'placeholder' => 'Renseigner un nouveau mot de passe',
                    ],
                    'hash_property_path' => 'password',
                ],
                'second_options' => [
                    'label' => 'Confirmation nouveau mot de passe',
                    'attr' => [
                        'placeholder' => 'Confirmer le mot de passe',
                    ]
                ],
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer les modifications',
                'attr' => [
                    'class' => 'btn btn-success float-right',
                ]
            ])
            //écouteur d'événement à la soumission du formulaire
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $user = $form->getData();
                // Récupérer le hasher depuis les options du formulaire passé depuis le controller
                $passwordHasher = $form->getConfig()->getOptions()['passwordHasher'];
                $isSamePassword = $passwordHasher->isPasswordValid($user, $form->get('actualPassword')->getData());
               // les mot de passe ne sont pas identique ('ancien vs nouveau')
                if (!$isSamePassword) {
                    //ajouter un message d'erreur au champs
                    $form->get('actualPassword')->addError(new FormError('Mot de passe actuel invalide'));
               }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'passwordHasher'=>null
        ]);
    }
}
