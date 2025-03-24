<?php

namespace App\Controller\Admin;

use App\Entity\HomeSlider;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class HomeSliderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return HomeSlider::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title')->setLabel('Titre'),
            TextEditorField::new('description')->setLabel('Description'),
            TextField::new('link_label')->setLabel('Libelle')->setHelp('Libelle du Lien '),
            TextField::new('link_target')->setLabel('Lien')->setHelp('Adresse URL du Lien '),
            ImageField::new('image')->setLabel('Image')
                ->setRequired($pageName !== 'edit')
                ->setHelp('Image du slider 1600x600')
                ->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]')
                ->setBasePath('/uploads/home-slider')
                ->setUploadDir('public/uploads/home-slider'),
            BooleanField::new('state')->setLabel('Active'),

        ];
    }

}
