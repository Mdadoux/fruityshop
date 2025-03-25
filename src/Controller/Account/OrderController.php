<?php


namespace App\Controller\Account;

use App\Repository\OrderRepository;
use App\Services\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    #[Route('/compte/mes-commandes', name: 'app_account_orders')]
    public function index(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy([
            'user' => $this->getUser(),
            'state' => [2, 3]
        ]);

        return $this->render('account/order/orders.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/compte/mes-commandes/{id_order}', name: 'app_account_order_show')]
    public function showOrder($id_order, OrderRepository $orderRepository,StripeService $myStripeService): Response
    {

        $order = $orderRepository->findOneBy(['id' => $id_order, 'user' => $this->getUser()]);
        if (!$order) {
           return $this->redirectToRoute('app_account_orders');
        }
        $paymentMethod = $myStripeService->getPaymentMethod($order->getStripePaymentSessionId());

        return $this->render('account/order/order-show.html.twig', [
            'order' => $order,
            'paymentMethod' => $paymentMethod,
        ]);
    }
}
