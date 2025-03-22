<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Services\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PaymentController extends AbstractController
{
    #[Route('/commande/paiement/{id_order}', name: 'app_payment')]
    public function index(int $id_order, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {
        Stripe::setApiKey($_ENV['STRIPE_API_KEY']);
//      header('Content-Type: application/json');
        $YOUR_DOMAIN = $_ENV['SITE_DOMAIN'];
        $theOrder = $orderRepository->findOneBy(['id' => $id_order, 'user' => $this->getUser()]);
        if (!$theOrder) {
            return $this->redirectToRoute('app_home');
        }
        $stripeProducts = [];

        foreach ($theOrder->getOrderDetails() as $product) {
            $stripeProducts[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => number_format($product->getProductPriceTaxeIncl() * 100, 0, '', ''),
                    'product_data' => [
                        'name' => $product->getProductName(),
                        'images' => [
                            $YOUR_DOMAIN . '/uploads/p-imgs/' . $product->getProductImage()
                        ]
                    ]
                ],
                'quantity' => $product->getProductQty(),
            ];
        }
        $stripeProducts[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => number_format($theOrder->getCarrierPrice() * 100, 0, '', ''),
                'product_data' => [
                    'name' => 'Livraison: ' . $theOrder->getCarrierName()
                ]
            ],
            'quantity' => 1,
        ];

        //https://docs.stripe.com/api/checkout/sessions/create
        $checkout_session = Session::create([
            'customer_email' => $theOrder->getUser()->getEmail(),
            'line_items' => [[
                $stripeProducts
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/confirmation/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/panier/annulation',
        ]);
        $theOrder->setStripePaymentSessionId($checkout_session->id);
        $entityManager->flush();
        // dd($checkout_session);
        return $this->redirect($checkout_session->url);

    }

    #[Route('/commande/confirmation/{stripe_session_id}', name: 'app_payment_confirmation')]
    public function success($stripe_session_id, OrderRepository $orderRepository,EntityManagerInterface $entityManager, CartService $cartService): Response
    {
        $order = $orderRepository->findOneBy(['stripe_payment_session_id' => $stripe_session_id, 'user' => $this->getUser()]);
        if (!$order) {
            return $this->redirectToRoute('app_home');
        }
        if ($order->getState() == 1) {
            $order->setState(2);
            $cartService->clearCart();//vider le panier après le paiment
            $entityManager->flush();
        }
        return $this->render('payment/payment-success.html.twig', [
            'order' => $order,
        ]);
    }

}
