<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Produit')
            ->setEntityLabelInPlural('Produits');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')->setLabel('Titre produit'),
            SlugField::new('slug')->setHelp('Url du produit')->setTargetFieldName('name'),
            NumberField::new('price')->setLabel('Prix H.T')->setHelp('Prix du produit ht'),
            ChoiceField::new('tva')->setLabel('Taux de TVA')->setChoices([
                '5%'=> '5.5',
                '10%'=> '10',
                '20%'=> '20',
            ]),
            /*https://symfony.com/bundles/EasyAdminBundle/current/fields/ImageField.html*/
            ImageField::new('image')->setLabel('Image')
                ->setRequired($pageName !== 'edit')
                ->setHelp('Image du produit 800x800')
                ->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]')
                ->setBasePath('/uploads/p-imgs')
                ->setUploadDir('public/uploads/p-imgs'),
            AssociationField::new('category')
                ->setLabel('Catégorie'),
            TextEditorField::new('description')->setHelp('Description produit'),
        ];
    }

}
