<?php

namespace App\Controller;

use App\Services\CartService;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    #[Route('/panier', name: 'app_cart')]
    public function index(CartService $cart): Response
    {
        return $this->render('cart/cart-index.html.twig', [
            // récupérer le panier en session de la classe CartService dédié
            'cart' => $cart->getCart(),
            'totalPriceTt' => $cart->getCartTotalPrice(),
            'totalPriceHt' => $cart->getCartTotalPrice(false),
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_dd')]
    public function add($id, CartService $cart, ProductRepository $productRepository, Request $request): Response
    {
        $origin = $request->headers->get('referer');
        $product = $productRepository->find($id);
        $cart->add($product);
        $this->addFlash('success', 'Produit ajouté au panier');
        return $this->redirect($origin);

    }

    #[Route('/cart/remove/{id}', name: 'app_cart_rm')]
    public function remove($id, CartService $cart): Response
    {

        $cart->remove($id);
        return $this->redirectToRoute('app_cart');

    }

    #[Route('/cart/delete/{id}', name: 'app_cart_delete')]
    public function deleteFromCart($id, CartService $cart): Response
    {
        $cart->delete($id);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/empty', name: 'app_cart_empty')]
    public function emptyCart(CartService $cart): Response
    {
        $cart->clearCart();
        return $this->redirectToRoute('app_home');

    }
}
