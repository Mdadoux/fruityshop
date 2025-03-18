<?php

namespace App\Services;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{

    public function __construct(private RequestStack $requestStack)
    {
    }

    /**
     * @param Product $product
     * @return void
     */
    /*Ajouter un produit dans le panier*/
    public function add(Product $product): void
    {
        //panier
        $cart = $this->getCart();
        // Le produit est dejà dans le panier
        if (isset($cart[$product->getId()])) {
            $cart[$product->getId()] = [
                'product' => $product,
                'qty' => $cart[$product->getId()]['qty'] + 1
            ];
        } else {
            $cart[$product->getId()] = [
                'product' => $product,
                'qty' => 1
            ];
        }
        //Sauvegarder les données en session panier
        $this->requestStack->getSession()->set('cart', $cart);

    }

    /**
     * @param int $id
     * @return void
     */
    //Suppression de la quantité produit dans le panier et supprimer si qty à 1
    public function remove(int $id): void
    {
        // le panier en session
        $cart = $this->getCart();
        if (isset($cart[$id]) && $cart[$id]['qty'] > 1) {
            $cart[$id]['qty']--;
        } else {
            // la quntité est à 1 on en lève le produit
            unset($cart[$id]);
        }
        $this->requestStack->getSession()->set('cart', $cart);
    }

    //Supprimer un produit dans le panier
    public function delete(int $id): void
    {
        $cart = $this->getCart();
        if (isset($cart[$id])) {
            unset($cart[$id]);
        }
        $this->requestStack->getSession()->set('cart', $cart);
    }


    // Vider le panier
    public function clearCart()
    {
        return $this->requestStack->getSession()->remove('cart');

    }

    public function getCartQty(): int
    {
        $cart = $this->getCart();
        $totalQty = 0;
        foreach ($cart as $product) {
            $totalQty += $product['qty'];
        }

        return $totalQty;
    }

    public function getCartTotalPrice(bool $isTaxe = true): float
    {
        $cart = $this->getCart();
        $totalPrice = 0;
        if ($isTaxe) {
            foreach ($cart as $item) {
                $totalPrice += $item['qty'] * $item['product']->getPriceWithTaxe();
            }
        } else {
            foreach ($cart as $item) {
                $totalPrice += $item['qty'] * $item['product']->getPrice();
            }
        }
        return ($totalPrice);
    }


    public function getCart(): array
    {
        return $this->requestStack->getSession()->get('cart', []);
    }
}