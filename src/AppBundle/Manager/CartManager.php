<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Product;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CartManager
{
    /**
     * @var RegistryInterface
     */
    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getProductsForDisplay(array $cart)
    {
        $productRepository = $this->doctrine->getRepository(Product::class);

        $products = [];
        $totalAmount = 0;

        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id);

            $products[$id]['product'] = $product;
            $products[$id]['qty'] = $quantity;

            $totalAmount += $product->getPrice() * $quantity;
        }

        return [
            'products' => $products,
            'totalAmount' => $totalAmount,
        ];
    }
}