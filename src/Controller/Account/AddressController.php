<?php


namespace App\Controller\Account;

use App\Entity\Address;
use App\Form\AddressUserType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AddressController extends AbstractController
{
    //Liste des adresses
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/mes-adresses', name: 'app_account_addresses')]
    public function index(): Response
    {
        return $this->render('account/address/adresses-index.html.twig', [
            'addresses' => $this->getUser()->getAddresses(),
        ]);

    }

    #[Route('/compte/mes-adresses/ajouter/{id}', name: 'app_account_addresses_from', defaults: ['id' => null])]
    public function adresseForm(Request $request, AddressRepository $addressRepository, $id): Response
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
        return $this->render('account/address/address-form.html.twig', [
            'userAddressForm' => $form->createView(),
        ]);

    }


    #[Route('compte/adresse/delete/{id}', name: 'app_account_addresses_delete')]
    public function addressDelete($id, AddressRepository $addressRepository): Response
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
