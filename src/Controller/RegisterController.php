<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterUserType;
use App\Services\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request,EntityManagerInterface $entityManager): Response
    {

        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success','Votre compte a bien été crée, vous pouvez à présent vous connecter ! 👍');

            //Envoie d'email de notification
            $mail = new MailService();
            $vars = [
                'firstname' => 'John',
                'siteUrl' => $_ENV['SITE_DOMAIN'],
            ];
            $mail->send($user->getEmail(), $user->getFirstname(),'Bienvenue chez Fruityshop ! 🍎🍊🍋','welcome.html', $vars);
            return $this->redirectToRoute('app_login');
        }
        return $this->render('register/register-from.html.twig', [
            'registerFrom'=> $form->createView(),
        ]);
    }
}
