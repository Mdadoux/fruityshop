<?php

namespace App\Controller\Account;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WishlistController extends AbstractController
{
    #[Route('/compte/liste-d-envie', name: 'app_account_wishlist')]
    public function index(): Response
    {
        return $this->render('account/wishlist/wishlist-index.html.twig', [
            'controller_name' => 'WishlistController',
        ]);
    }

    #[Route('/compte/liste-d-envie/add/{id}', name: 'app_account_wishlist_add')]
    public function addToWishList($id, ProductRepository $productRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $product = $productRepository->find($id);
        $origine = $request->headers->get('referer');
        if ($product) {
            $this->getUser()->addWishlist($product);
            $entityManager->flush();
            $this->addFlash('success', 'Le produit a bien été ajouté dans votre list d\'envie.');
        }
        if (!$origine) {
            return $this->redirectToRoute('app_account_wishlist');
        } else {
            return $this->redirect($origine);
        }
    }

    #[Route('/compte/liste-d-envie/remove/{id}', name: 'app_account_wishlist_remove')]
    public function removeFromWishList($id, ProductRepository $productRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $product = $productRepository->find($id);
        $origine= $request->headers->get('referer');
        if ($product) {
            $this->getUser()->removeWishlist($product);
            $entityManager->flush();
            $this->addFlash('success', 'Le produit  a bien été retirer de votre liste d\'envie!');
        } else {
            $this->addFlash('danger', 'Ce produit n\'existe pas');
        }

        if (!$origine) {
            return $this->redirectToRoute('app_account_wishlist');
        } else {
            return $this->redirect($origine);
        }

    }
}
