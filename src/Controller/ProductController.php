<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/produit', name: 'app_product_')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('product/product.html.twig');
    }


    #[Route('/{slug}', name: 'details')]
    public function detailsProduct(Product $product): Response
    {
//        dd($product->getCategories());
        return $this->render('product/detailsProduct.html.twig', [
            'product' => $product
        ]);
    }
}