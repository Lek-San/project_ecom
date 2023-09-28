<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories', name: 'app_category_')]
class CategoryController extends AbstractController
{
    /**
     * @param Category $category
     * @return Response
     */
    #[Route('/{slug}', name: 'list')]
    public function index(
        Category $category,
        ProductRepository $productRepository,
        Request $request
    ): Response
    {
        //Chercher le numero de la page dans l'url
        $page = $request->query->getInt('page', 1);
        // Chercher la liste des produit de la catégorie
        $products = $productRepository->findProductPaginated($page, $category->getSlug(), 2);

        return $this->render('categories/list.html.twig', [
            'categories' => $category,
            'products' => $products,
        ]);
    }
}
