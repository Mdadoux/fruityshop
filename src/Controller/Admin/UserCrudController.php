<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setPageTitle('index','Gérez vos clients')
            ->setEntityLabelInPlural('Utilisateurs');
    }


    public function configureFields(string $pageName): iterable
    {
        return [

            TextField::new('firstname')->setLabel('Prénom'),
            DateField::new('last_login_at')->setLabel('Dernière connexion')->onlyOnIndex(),
            TextField::new('lastname')->setLabel('Nom'),
            TextField::new('email')->setLabel('Email')->onlyOnIndex(),
            ChoiceField::new('roles')->setChoices([
                'Client' => 'ROLE_USER',
                'Administrateur' => 'ROLE_ADMIN',
            ])->allowMultipleChoices()->setLabel('Permissions'),
        ];
    }

}
