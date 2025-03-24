<?php

namespace App\Controller;


use App\Repository\HomeSliderRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(HomeSliderRepository $sliderRepository,ProductRepository $productRepository): Response
    {
        $sliders = $sliderRepository->findBy([
            'state' => true
        ]);
        $featuredProducts =  $productRepository->findBy([
            'isHomepage' => true
        ]);
        return $this->render('home/index.html.twig', [
            'sliders' => $sliders,
            'featuredProducts' => $featuredProducts,
        ]);
    }
}
