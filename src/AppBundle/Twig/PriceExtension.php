<?php

namespace AppBundle\Twig;

class PriceExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('priceTTC', [$this, 'calculPriceTTC']),
        ];
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('displayPrice', [$this, 'displayPrice']),
        ];
    }

    public function displayPrice($price)
    {
        // Je récupère la devise de l'utilisateur courant

        return $price . ' €';
    }

    public function calculPriceTTC($price)
    {
        return $price * 1.2;
    }
}