<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('admin/produits', name:'app_admin_products_')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/products/index.html.twig');
    }
    #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        //Création d'un nouveau produit
        $product = new Product();

        // Création du formulaire
        $productForm = $this->createForm(ProductFormType::class, $product);

        // Traiter la requête du formulaire
        $productForm->handleRequest($request);

        // Verifier si le formulaire est soumit et valide
        if ($productForm->isSubmitted() && $productForm->isValid()){
            // Génération du slug
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);

            //Arrondir le prix en centimes
            // $price = $product->getPrice() * 100;
            // $product->setPrice($price);

            // Stocker le produit en bdd
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Produit ajouté avec succès !');

            //Redirection
            return $this->redirectToRoute('app_admin_products_index');
        }

        return $this->render('admin/products/add.html.twig', [
            'productForm' => $productForm->createView()
        ]);
        // Un return avec une autre syntax
        // return $this->renderForm('admin/products/add.html.twig', compact('productForm'));

    }

    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Product $product, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        //Vérifier si l'utilisateur peut éditer avec le Voter
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);

        // Création du formulaire
        $productForm = $this->createForm(ProductFormType::class, $product);

        // Traiter la requête du formulaire
        $productForm->handleRequest($request);

        // Verifier si le formulaire est soumit et valide
        if ($productForm->isSubmitted() && $productForm->isValid()){
            // Génération du slug
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);

            //Arrondir le prix en centimes
            // $price = $product->getPrice() * 100;
            // $product->setPrice($price);

            // Stocker le produit en bdd
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Produit a était modifier avec succès !');

            //Redirection
            return $this->redirectToRoute('app_admin_products_index');
        }

        return $this->render('admin/products/edit.html.twig', [
            'productForm' => $productForm->createView()
        ]);
        // Un return avec une autre syntax
        // return $this->renderForm('admin/products/edit.html.twig', compact('productForm'));
    }

    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Product $product): Response
    {
        //Vérifier si l'utilisateur peut supprimer avec le Voter
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);
        return $this->render('admin/products/index.html.twig');
    }
}