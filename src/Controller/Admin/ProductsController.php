<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use App\Entity\Product;
use App\Form\ProductFormType;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function add(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        PictureService $pictureService
    ): Response
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
            // Récuperation des images
            $pictures = $productForm->get('images')->getData();

            foreach ($pictures as $picture){
                // Définir le dossier de destination
                $folder = 'products';
                // Appeller le service d'ajout
                $fichier = $pictureService->add($picture, $folder, 300, 300);

                $image = new Image();
                $image->setName($fichier);
                $product->addImage($image);

            }

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
    public function edit(
        Product $product,
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        PictureService $pictureService
    ): Response
    {
        //Vérifier si l'utilisateur peut éditer avec le Voter
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);

        // Création du formulaire
        $productForm = $this->createForm(ProductFormType::class, $product);

        // Traiter la requête du formulaire
        $productForm->handleRequest($request);

        // Verifier si le formulaire est soumit et valide
        if ($productForm->isSubmitted() && $productForm->isValid()){
            // Récuperation des images
            $pictures = $productForm->get('images')->getData();

            foreach ($pictures as $picture){
                // Définir le dossier de destination
                $folder = 'products';
                // Appeller le service d'ajout
                $fichier = $pictureService->add($picture, $folder, 300, 300);

                $image = new Image();
                $image->setName($fichier);
                $product->addImage($image);

            }
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
            'productForm' => $productForm->createView(),
            'product' => $product
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

    #[Route('/suppression/image/{id}', name: 'delete_picture', methods: ['DELETE'])]
    public function deletePicture(
        Image $image,
        Request $request,
        EntityManagerInterface $entityManager,
        PictureService $pictureService
    ): JsonResponse
    {
        // Recuperation du contenu de la requête
        $data = json_decode($request->getContent(), true);

        if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])){
            // Le token csrf est valide | si valide recuperation du nom de l'image
            $name = $image->getName();
            if ($pictureService->delete($name, 'products', 300, 300)){
                // Suppression de l'image de la BDD
                $entityManager->remove($image);
                $entityManager->flush();

                return new JsonResponse(['success' => true], 200);
            }

            return new JsonResponse(['error' => 'Erreur de suppression de l\'image'], 400);
        }

        return new JsonResponse(['error' => 'Tocke invalide'], 400);
    }
}