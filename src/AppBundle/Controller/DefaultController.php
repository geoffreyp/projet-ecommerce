<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Product;
use AppBundle\Form\CommentType;
use AppBundle\Form\ContactType;
use AppBundle\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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

        return $this->render('default/index.html.twig', [
            'products' => $products,
        ]);
    }

    public function showCategoriesMenuAction()
    {
        $categoryRepository = $this->get('doctrine')->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        return $this->render('default/menu.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/product/{id}", requirements={"page"="\d+"}, name="product_details")
     */
    public function showProductAction(Request $request, Product $product)
    {
        $productRepository = $this->get('doctrine')->getRepository(Product::class);
        $otherProducts = $productRepository->getRandomProducts($product);

        shuffle($otherProducts);
        $otherProducts = array_slice($otherProducts, null, 2);

        // créer un objet PHP Comment
        $comment = new Comment($product);

        // créer notre formulaire
        $form = $this->createForm(CommentType::class, $comment);

        // gérer la requête HTTP
        $form->handleRequest($request);

        // soit le formulaire est KO => on affiche les erreurs
        if ($form->isSubmitted() && $form->isValid())
        {
            // soit le formulaire est OK => on redirige
            $em = $this->get('doctrine')->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('default/product.html.twig', [
            'product' => $product,
            'other_products' => $otherProducts,
            'comment_form' => $form->createView(),
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

    /**
     * @Route("/contact", name="contact")
     */
    public function contactAction(Request $request)
    {
        $contact = new Contact();

        // Formulaire
        $form = $this->createForm(ContactType::class, $contact);

        // Gestion de la requête
        $form->handleRequest($request);

        // Redirection
        if ($form->isValid() && $form->isSubmitted())
        {
            $em = $this->get('doctrine')->getManager();
            $em->persist($contact);
            $em->flush();

            $this->addFlash('info', 'confirmation_success');

            return $this->redirectToRoute('homepage');
        }

        // Affichage
        return $this->render('default/contact.html.twig', [
            'contact_form' => $form->createView(),
        ]);
    }
}
