<?php

namespace App\Controller\Admin;

use App\Entity\Carrier;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\Image;

class CarrierCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Carrier::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Transporteur')
            ->setEntityLabelInPlural('Transporteurs');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name')->setLabel('Nom du transporteur'),
            NumberField::new('price')->setLabel('Prix T.T')->setHelp('Prix de livraison T.T.C sans €'),
            TextareaField::new('description'),
            ImageField::new('image')->setLabel('Logo du transporteur')
                ->setRequired($pageName !== 'edit')
                ->setFileConstraints(new Image(maxWidth: '300', maxHeight: '300'))
                ->setHelp('Image du produit 250x250')
                ->setUploadedFileNamePattern('[slug]-[contenthash].[extension]')
                ->setBasePath('/uploads/carrier')
                ->setUploadDir('public/uploads/carrier'),
        ];
    }

}
