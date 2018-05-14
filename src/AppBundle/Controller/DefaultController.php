<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    const NUMBER_ITEMS_HOMEPAGE = 5;

    /**
     * @Route("/", name="homepage")
     * @return Response
     */
    public function homepageAction()
    {
        $productRepository = $this->get('doctrine')->getRepository(Product::class);

        $products = $productRepository->getLastActiveProducts(self::NUMBER_ITEMS_HOMEPAGE);
        //$products = $repository->getLastActiveProducts($this->getParameter('products_homepage'));

        $categoryRepository = $this->get('doctrine')->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        return $this->render('default/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/category/{id}", requirements={"page"="\d+"}, name="category_details")
     */
    public function showCategoryAction(Category $category)
    {
        return $this->render('default/category.html.twig', [
            'category' => $category,
        ]);
    }
}
