<?php

namespace App\Controller\Account;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/compte', name: 'app_account')]
    /*#[IsGranted('ROLE_USER')]*/
    public function index(): Response
    {
        return $this->render('account/account-index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
