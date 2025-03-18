<?php

namespace App\Twig;

use App\Repository\CategoryRepository;
use App\Services\CartService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class AppExtensions extends AbstractExtension implements GlobalsInterface
{
    private $categoryRepository;
    private $cartService;

    public function __construct(CategoryRepository $categoryRepository, CartService $cartService)
    {
        $this->categoryRepository = $categoryRepository;
        $this->cartService = $cartService;

    }

    public function getFilters()
    {

        return [
            new TwigFilter('price_fromat', [$this, 'formatPrice']),
        ];
    }

    public function formatPrice($price)
    {

        return number_format($price, 2, ',') . '€';

    }

    // Récupérer des variables de manière globale
    public function getGlobals(): array
    {

        return [
            'categoryList' => $this->categoryRepository->findAll(),
            'cartQty' => $this->cartService->getcartQty(),
        ];
    }


}
