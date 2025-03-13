<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AccountController extends AbstractController
{
    #[Route('/compte', name: 'app_account')]
    /*#[IsGranted('ROLE_USER')]*/
    public function index(): Response
    {
        return $this->render('account/user-account.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }
}
