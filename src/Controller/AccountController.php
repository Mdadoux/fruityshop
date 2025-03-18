<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressUserType;
use App\Form\PasswordUserType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AccountController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte', name: 'app_account')]
    /*#[IsGranted('ROLE_USER')]*/
    public function index(): Response
    {
        return $this->render('account/user-account.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }

    #[Route('/compte/update-password', name: 'app_update_password')]
    public function updatePassword(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(PasswordUserType::class, $user, [
            'passwordHasher' => $passwordHasher,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Votre mot de passe a bien été modifié ! ☺️');
        }
        return $this->render('account/user-password.html.twig', [
            'userPasswdForm' => $form->createView(),
        ]);
    }

    //Liste des adresses
    #[Route('/compte/mes-adresses', name: 'app_account_addresses')]
    public function userAdresses(): Response
    {
        return $this->render('account/user-adresses.html.twig', [
            'addresses' => $this->getUser()->getAddresses(),
        ]);

    }

    #[Route('/compte/mes-adresses/ajouter/{id}', name: 'app_account_addresses_from', defaults: ['id' => null])]
    public function userAdresseForm(Request $request, AddressRepository $addressRepository, $id): Response
    {
        if ($id) {
            $address = $addressRepository->find($id);
            if (!$address or $address->getUser() !== $this->getUser()) {
                return $this->redirectToRoute('app_account_addresses');
            }
        } else {
            $address = new Address();
            //Ne pas oublier d'attacher l'adresse à l'utilisateur en cours
            $address->setUser($this->getUser());
        }
        $form = $this->createForm(AddressUserType::class, $address);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($address);
            $this->entityManager->flush();
            $this > $this->addFlash('success', 'Adresse ajouté à votre liste d\'adresses 🎉');
            return $this->redirectToRoute('app_account_addresses');
        }
        return $this->render('account/user-adresses-form.html.twig', [
            'userAddressForm' => $form->createView(),
        ]);

    }

    #[Route('compte/adresse/delete/{id}', name: 'app_account_addresses_delete')]
    public function deleteAddress($id, AddressRepository $addressRepository): Response
    {

        $address = $addressRepository->find($id);
        if (!$address or $address->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('app_account_addresses');
        }
        $this->entityManager->remove($address);
        $this->entityManager->flush();
        $this->addFlash('success', 'L\'Adresse a bien été supprimée ! ');
        return $this->redirectToRoute('app_account_addresses');
    }


}
