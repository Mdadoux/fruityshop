<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\OrderType;
use App\Services\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    private $cartService;
    private $totalPriceExclTax;
    private $totalPriceInclTax;
   public function __construct(CartService $cartService)
   {
       $this->cartService = $cartService;
       $this->totalPriceExclTax = $this->cartService->getCartTotalPrice(false);
       $this->totalPriceInclTax = $this->cartService->getCartTotalPrice(true);
   }


    #[Route('/commande/livraison', name: 'app_order')]
    public function index(): Response
    {
        //Afficher juste le formulaire et les données
        $addresses = $this->getUser()->getAddresses();
        $isAddress = false;
        if (count($addresses) == 0) {
            $isAddress = true;
        }
        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $addresses,
            'action' => $this->generateUrl('app_order_summary'),// redirige vers un autre url
        ]);
        return $this->render('order/order-index.html.twig', [
            'orderDelivery' => $form->createView(),
            'isAddress' => $isAddress,
            'cart' => $this->cartService->getCart(),
            'totalPriceTt' => $this->totalPriceInclTax,
            'totalPriceHt' => $this->totalPriceExclTax,
        ]);
    }

    #[Route('/commande/recapitulatif', name: 'app_order_summary')]
    public function orderAdd(Request $request,EntityManagerInterface $entityManager): Response
    {
        //Eviter erreur au cas où le formulaire n'a pas été sous depuis l'étape precedent
       if ($request->getMethod() !=='POST') {
           return $this->redirectToRoute('app_cart');
       }
        $cart = $this->cartService->getCart();
        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $this->getUser()->getAddresses()
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //transformer le panier en commande
            $addressObj = $form->get('addresses')->getData();
            //Construire l'adresse de livraison
            $addressDelivery = $addressObj->getFirstname().' '.$addressObj->getLastname().'<br>';
            $addressDelivery .= $addressObj->getAddress().'<br>';
            $addressDelivery .= $addressObj->getPostal().' '.$addressObj->getCity().' - ';
            $addressDelivery .= $addressObj->getcountry().'<br>';
            $addressDelivery .= $addressObj->getPhone();

            $order = new Order();
            $order->setCreatedAt(new \DateTime());//associer la date
            $order->setState(1);//associer un statut
            $order->setCarrierPrice($form->get('carrier')->getData()->getPrice());//Prix du transporteur
            $order->setCarrierName($form->get('carrier')->getData()->getName());//Nom du transporteur
            $order->setDelivery($addressDelivery);//l'adresse de livraison mappée plus haut
            $order->setUser($this->getUser()); // L'utilisateur actuel devient le commanditaire
            //ajoute les details du panier dans order detail
            foreach ($cart as $item) {
                $orderDetail = new OrderDetail();
                $product = $item['product'];
                $orderDetail->setProductName($product->getName());
                $orderDetail->setProductPrice($product->getPrice());
                $orderDetail->setProductImage($product->getImage());
                $orderDetail->setProductTva($product->getTva());
                $orderDetail->setProductQty($item['qty']);
                //gerffer oder detail dans order
                $order->addOrderDetail($orderDetail);
            }
            $entityManager->persist($order);
            $entityManager->flush();
        }

        return $this->render('order/order-summary.html.twig', [
            'selectedOptions' => $form->getData(),
            'cart' => $cart,
            'totalPriceTt' => $this->totalPriceInclTax,
            'totalPriceHt' => $this->totalPriceExclTax,
        ]);
    }
}
