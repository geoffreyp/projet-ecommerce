<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Entity\CartProduct;
use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CartController extends Controller
{
    /**
     * @Route("/add/{id}", requirements={"id"="\d+"} ,name="add_to_cart")
     *
     * @param Product $product
     * @throws \Exception
     */
    public function addToCart(Product $product)
    {
        // gestion du stock
        $currentStock = $product->getStock();

        if ($currentStock === 0) {
            throw new \Exception('Produit indisponible');
        }

        $product->decrementStock();

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        $session = $this->get('session');

        $cart = $session->get('cart');

        $currentQty = $cart[$product->getId()] ?? 0;

        // on ajoute le produit au panier
        $cart[$product->getId()] = $currentQty + 1;

        $session->set('cart', $cart);
        $session->save();

        $this->addFlash('info', 'Le produit ' . $product->getName(). ' a bien été ajouté.');

        return $this->redirectToRoute('product_details', ['id' => $product->getId() ]);
    }

    /**
     * @Route("/cart", name="cart")
     */
    public function cartAction()
    {
        $cart = $this->get('session')->get('cart') ?? [];

        $productRepository = $this->get('doctrine')->getRepository(Product::class);

        $products = [];
        $totalAmount = 0;

        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id); // + on aura de produit, + on fera de requête ...

            $products[$id]['product'] = $product;
            $products[$id]['qty'] = $quantity;

            $totalAmount += $product->getPrice() * $quantity;
        }

        return $this->render('cart/details.html.twig', [
            'products' => $products,
            'totalAmount' => $totalAmount,
        ]);
    }

    /**
     * @Route("/remove/{id}", requirements={"id"="\d+"} ,name="remove_from_cart")
     */
    public function removeFromCart(Product $product)
    {
        $session = $this->get('session');
        $cart = $session->get('cart');

        // remettre le stock en base
        $product->provisionStock($cart[$product->getId()]);

        $em = $this->get('doctrine')->getManager();
        $em->persist($product);
        $em->flush();

        // supprimer le produit du panier en session
        unset($cart[$product->getId()]);

        $session->set('cart', $cart);
        $session->save();

        return $this->redirectToRoute('cart');
    }
}