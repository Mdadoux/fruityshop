<?php

namespace App\Twig;

use App\Repository\CategoryRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class AppExtensions extends AbstractExtension implements GlobalsInterface
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;

    }

    public function getFilters()
    {

        return [
            new TwigFilter('price_fromat', [$this, 'formatPrice']),
        ];
    }

    public function formatPrice($price)
    {
        /*if ($mode) {
            $formattedPrice = explode(',', number_format($price, 2, ','));
            return sprintf('%s%s%s%s%s%s%s€%s', '<span>', $formattedPrice[0], '</span>', '<span>', $formattedPrice[1], '</span>', '<span>', '</span>');
        } else*/
        return number_format($price, 2, ',') . '€';

    }

    // Recuperer des variable de manière globale
    public function getGlobals(): array
    {

        return [
            'categoryList' => $this->categoryRepository->findAll()
        ];
    }


}
