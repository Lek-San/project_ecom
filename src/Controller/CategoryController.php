<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function index(Category $category): Response
    {
        $products = $category->getProducts();

        return $this->render('category/list.html.twig', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}
