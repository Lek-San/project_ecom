<?php

namespace App\Controller\Admin;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/categories', name: 'app_admin_categories_')]
class CategoryController extends AbstractController
{
    #[Route(path: '/', name: 'index')]
    public function index(
        CategoryRepository $categoryRepository
    ): Response
    {
        $categories = $categoryRepository->findBy([], ['categoryOrder' => 'asc']);

        return $this->render('admin/categories/index.html.twig', [
            'categories' => $categories
        ]);
    }
}