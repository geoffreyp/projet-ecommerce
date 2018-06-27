<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Entity\CartProduct;
use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CartController extends Controller
{
    const MAX_VIEWED_PRODUCTS = 3;

    /**
     * @Route("/add", name="add_to_cart")
     * @Method("POST")
     *
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function addToCart(Request $request)
    {
        $productRepository = $this->get('doctrine')->getRepository(Product::class);
        $product = $productRepository->find($request->get('product_id'));
        $qty = $request->get("qty");

        for($i = 0; $i < $qty; $i++) {
            $this->get('app.cart')->addProduct($product);
        }

        $this->addFlash('info', 'Le produit ' . $product->getName(). ' a bien été ajouté '. $qty .' fois <a href="'.$this->generateUrl('cart').'">Voir le panier.</a>');

        return $this->redirectToRoute('product_details', ['id' => $product->getId() ]);
    }

    /**
     * @Route("/cart", name="cart")
     */
    public function cartAction()
    {
        $cart = $this->get('session')->get('cart') ?? [];

        $display = $this->get('app.cart')->getProductsForDisplay($cart);

        $productRepository = $this->get('doctrine')->getRepository(Product::class);
        $mostViewedProducts = $productRepository->getAllMostViewedProducts(self::MAX_VIEWED_PRODUCTS);

        return $this->render('cart/details.html.twig', [
            'products' => $display['products'],
            'totalAmount' => $display['totalAmount'],
            'most_viewed_products' => $mostViewedProducts,

        ]);
    }

    /**
     * @Route("/remove", name="remove_from_cart")
     * @Method("POST")
     */
    public function removeFromCart(Request $request)
    {
        $productRepository = $this->get('doctrine')->getRepository(Product::class);
        $product = $productRepository->find($request->get('product_id'));

        $this->get('app.cart')->removeProduct($product);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/clean", name="clean_cart")
     * @Method("POST")
     */
    public function clean(Request $request){
        $productRepository = $this->get('doctrine')->getRepository(Product::class);
        $product_ids = $request->get('product_id');

        foreach ($product_ids as $id){
            $product = $productRepository->find($id);

            $this->get('app.cart')->removeProduct($product);
        }
        return $this->redirectToRoute('cart');
    }
}