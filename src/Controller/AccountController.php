<?php

namespace App\Controller;

use App\Form\PasswordUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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

    #[Route('/compte/update-password', name: 'app_update_password')]
    public function updatePassword(Request $request,UserPasswordHasherInterface $passwordHasher,EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(PasswordUserType::class, $user,[
            'passwordHasher' => $passwordHasher,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success','Votre mot de passe a bien été modifié ! ☺️');
        }
        return $this->render('account/user-password.html.twig', [
            'userPasswdForm' => $form->createView(),
        ]);
    }
}
