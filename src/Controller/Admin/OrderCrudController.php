<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Services\MailService;
use App\Services\OrderStatesService;
use Doctrine\ORM\EntityManagerInterface;
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
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;

class OrderCrudController extends AbstractCrudController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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
    public function showOrderDetail(AdminContext $context,AdminUrlGenerator $adminUrlGenerator,Request $request)
    {

        $order = $context->getEntity()->getInstance();
        $param = $request->get('orderState');
        if (isset($param)){
           $this->changeOrderState($order,$param);
        }
        return $this->render('admin/order/order-details.html.twig', [
            'order' => $order,
            'orderStates' => OrderStatesService::STATES,
            'crudController' => self::class,
        ]);

    }

    /**
     * @param $order
     * @param $newState
     * @return void
       Permettre de modifier le statut de la commande
     */

    public function changeOrderState($order,$order_state_id)
    {
       $order->setState($order_state_id);
       $this->entityManager->flush();
       //Récupérer l'état de la commande
       $order_state = (object)OrderStatesService::STATES[$order_state_id];
       // Notifier l'utilisateur du succès de l'opération
       $this->addFlash('success','L\'état de la commande à été modifié');
       // Ne pas oublier de notifier le client du changement de statut
       $client = $order->getUser();
       //dd($client->getEmail());
        $vars = [
            'firstname' => $client->getFirstname(),
            'order_id' => $order->getId(),

        ];
       $email= new MailService();
       $email->send($client->getEmail(),$client->getFirstName().' '.$client->getLastName(),$order_state->email_subject,$order_state->email_template, $vars);
        
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
