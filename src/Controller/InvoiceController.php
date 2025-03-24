<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class InvoiceController extends AbstractController
{
    /* Generation de la facture PDF pour le client dans le compte client */
    #[Route('/compte/facture/impression/{id_order}/{mode}', name: 'app_invoice',defaults: ['mode'=>null])]
    public function index(OrderRepository $orderRepository, $id_order,$mode): Response
    {
        $order = $orderRepository->findOneBy(['id' => $id_order]);
        //la facture appartient-elle à l'utilisateur ?
        if (!$order || $order->getUser()->getId() != $this->getUser()->getId()) {
            return $this->redirectToRoute('app_account');
        }

        $dompdf = new Dompdf();
        $html = $this->renderView('invoice/invoice-index.html.twig', [
            'order' => $order,
        ]);
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        if ($mode === 'download') {
            $dompdf->stream('facture_' . $order->getId() . '_' . $order->getUser()->getFirstname() . '_' . $order->getUser()->getLastname() . date_format($order->getCreatedAt(), '_d_m_Y') . '.pdf', []);
        } else {
            $dompdf->stream('facture_' . $order->getId() . '_' . $order->getUser()->getFirstname() . '_' . $order->getUser()->getLastname() . date_format($order->getCreatedAt(), '_d_m_Y') . '.pdf', [
                "Attachment" => false
            ]);
        }
        exit();
    }


    //facture coté admin
    #[Route('/admin/facture/impression/{id_order}/{mode}', name: 'app_admin_invoice', defaults: ['mode' => null])]
    public function adminInvoice(OrderRepository $orderRepository, $id_order, $mode): Response
    {

        $order = $orderRepository->findOneBy(['id' => $id_order]);
        //la facture appartient-elle à l'utilisateur ?
        if (!$order) {
            return $this->redirectToRoute('admin');
        }
        $dompdf = new Dompdf();
        $html = $this->renderView('invoice/invoice-index.html.twig', [
            'order' => $order,
        ]);
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        // Render the HTML as PDF
        $dompdf->render();
        // Output the generated PDF to Browser
        // afficher ou imprimer
        if ($mode === 'download') {
            $dompdf->stream('facture_' . $order->getId() . '_' . $order->getUser()->getFirstname() . '_' . $order->getUser()->getLastname() . date_format($order->getCreatedAt(), '_d_m_Y') . '.pdf', []);
        } else {
            $dompdf->stream('facture_' . $order->getId() . '_' . $order->getUser()->getFirstname() . '_' . $order->getUser()->getLastname() . date_format($order->getCreatedAt(), '_d_m_Y') . '.pdf', [
                "Attachment" => false
            ]);
        }
        exit();
    }


}
