<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle',TextType::class,[
                'label' => 'Titre de l\'adresse',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Domicile'
                ]
            ])
            ->add('firstname',TextType::class,[
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Votre nom'
                ]
            ])
            ->add('lastname',TextType::class,[
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Votre prénom'
                ]
            ])
            ->add('address',TextType::class,[
                'label' => 'Adresse',
                'attr' => [
                    'placeholder' => 'Ex: 2 rue bidon'
                ]
            ])
            ->add('city',TextType::class,[
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Votre ville'
                ]
            ])
            ->add('postal',TextType::class,[
                'label' => 'Code postal',
                'attr' => [
                    'placeholder' => 'Ex: 7500'
                ]
            ])
            ->add('phone',TextType::class,[
                'label'=> 'Numero de telephone',
                'attr' => [
                    'placeholder' => 'Ex: +33 (0) 0 000 000'
                ]
            ])
            ->add('country',CountryType::class,[
                'label' => 'Pays',
                'attr' => [
                    'placeholder' => 'Choisissez votre pays'
                ]
            ])
            ->add('save', SubmitType::class,[
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn btn-primary float-end mt-2'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
