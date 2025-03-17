<?php

namespace App\Classe;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{

    public function __construct(private RequestStack $requestStack)
    {
    }

    public function add(Product $product)
    {
        //panier
        $cart = $this->requestStack->getSession()->get('cart');
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
        //créer la session du panier
        $this->requestStack->getSession()->set('cart', $cart);

    }

    public function remove($id)
    {
        $cart = $this->requestStack->getSession()->get('cart');
        if (isset($cart[$id]) && $cart[$id]['qty'] > 1) {
            $cart[$id]['qty']--;
        } else {
            unset($cart[$id]);
        }
        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function dumpCart()
    {
        return $this->requestStack->getSession()->remove('cart');

    }

    public function getCart()
    {
        return $this->requestStack->getSession()->get('cart');
    }
}