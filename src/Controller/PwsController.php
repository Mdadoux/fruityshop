<?php

namespace App\Controller;

use App\Form\ForgotPasswordType;
use App\Form\ResetPswType;
use App\Repository\UserRepository;
use App\Services\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PwsController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/mot-de-passe-oublie', name: 'app_forgot_password')]
    public function index(Request $request, UserRepository $repository, EntityManagerInterface $entityManager): Response
    {
        //Générer le formulaire
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        // Traitement du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            //Verifier que l'adresse email renseigné se trouve dans la bdd
            $user = $repository->findOneBy(['email' => $form->get('email')->getData()]);
            if ($user) {
                // OUI
                //créer un token unique
                $token = bin2hex(random_bytes(32));
                // Atribuer le token à l'utilisateur
                $user->setToken($token);
                $date = new \DateTime();
                //https://www.php.net/manual/fr/datetime.modify.php
                $date->modify('+10 minutes ');
                $user->setTokenExpireAt($date);
                // sauvegarder le tout en BDD
                $entityManager->flush($user);
                $vars = [
                    'reset_link' => $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL),
                ];
                // Envoyer à l'utilisateur un email pour réinitialiser son mot de passe
                $email = new MailService();
                $email->send($user->getEmail(), $user->getFirstname(), 'Réinitialisation de mot de passe', 'forgot_password.html', $vars);
            }
            $this->addFlash('success', 'Si votre email existe, un email vous sera envoyer pour réinitialiser votre mot de passe');
        }
        //2 traitements du formulaire
        //3-  Si adresse mail existe enoyer email de nouveau mdp
        // 4 - sinon notofication
        return $this->render('psw/reset-password.html.twig', [
            'forgotPassForm' => $form->createView(),
        ]);
    }

    #[Route('/reset-password/reset/{token}', name: 'app_reset_password')]
    public function resetPassword(Request $request, UserRepository $userRepository, $token)
    {
        if (!$token) {
            return $this->redirectToRoute('app_forgot_password');
        }

        $user = $userRepository->findOneBy(['token' => $token]);
        $now = new \DateTime();
        if (!$user || $now > $user->getTokenExpireAt()) {
            return $this->redirectToRoute('app_forgot_password');
        }
        $form = $this->createForm(ResetPswType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          // Remettre à zéro le token
            $user->setToken(null);
            $user->setTokenExpireAt(null);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'Votre mot de passe a bien été modifié ! ☺️');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('psw/reset-password-form.html.twig', [
            'resetPassForm' => $form->createView(),
        ]);

    }
}
