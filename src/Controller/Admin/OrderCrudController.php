<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }
    public function configureCrud(Crud $crud): Crud{
        return $crud
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes');
    }
    public function configureActions(Actions $actions): Actions
    {
        //Créer une vue avec une action personnalisée
        //https://symfony.com/bundles/EasyAdminBundle/current/actions.html#adding-custom-actions
        $orderDetailView = Action::new('Afficher','Afficher','fa fa-search-plus')->linkToCrudAction('showOrderDetail');
        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, $orderDetailView)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::EDIT);

    }

    //https://github.com/EasyCorp/EasyAdminBundle/issues/6847
    #[AdminAction('{entityId}/show', 'admin_order_show')]
    public function showOrderDetail(AdminContext $context)
    {

        $order = $context->getEntity()->getInstance();
        return $this->render('admin/order/order-details.html.twig', [
            'order' => $order,
        ]);

    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateField::new('createdAt')->setLabel('Date'),
            NumberField::new('state')->setLabel('Statut')//une vue personnalisée pour les statuts
            ->setTemplatePath('admin/order/order-states.html.twig'),
            AssociationField::new('user')->setLabel('Client'),
            TextField::new('carrierName')->setLabel('Transporteur'),
            NumberField::new('getTotalTva')->setLabel('Total Tva')->setTemplatePath('admin/price-field-value.html.twig'),
            NumberField::new('getTotalTaxeIncl')->setLabel('Prix T.T.C')->setTemplatePath('admin/price-field-value.html.twig'),
        ];
    }

}
