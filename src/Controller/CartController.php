<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/panier', name: 'app_cart_')]
class CartController extends AbstractController
{
    #[Route(path: '/', name: 'index')]
    public function index(
        SessionInterface $session,
        ProductRepository $productRepository
    )
    {
        $cart = $session->get('panier', []);
        // Inisialiser les variable
        $data = [];
        $total = 0;

        foreach ($cart as $id => $quatity){
            $product = $productRepository->find($id);

            $data[] = [
                'product' => $product,
                'quantity' => $quatity
            ];
            $total += $product->getPrice() * $quatity;
        }

        return $this->render('cart/index.html.twig', [
            'data' => $data,
            'total' => $total
        ]);
    }

    #[Route(path: '/ajout/{id}', name: 'add')]
    public function add(
        Product $product,
        SessionInterface $session
    )
    {
        // Récuperation de l'id du produit
        $id = $product->getId();

        // Récupèration du panier existant
        $cart = $session->get('panier', []);

        // Ajouter le produit dans le panier s'il n'y est pas encore | sinon en incrémente sa quantité
        if (empty($cart[$id])){
            $cart[$id] = 1;
        } else {
            $cart[$id]++;
        }

        $session->set('panier', $cart);

        // Redirection vers la page du panier
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route(path: '/retirer/{id}', name: 'remove')]
    public function remove(
        Product $product,
        SessionInterface $session
    )
    {
        // Récuperation de l'id du produit
        $id = $product->getId();

        // Récupèration du panier existant
        $cart = $session->get('panier', []);

        // Retirer le produit du panier s'il n'y a qu'un exemplaire | sinon en décrémente sa quantité
        if (!empty($cart[$id])){
            if ($cart[$id] > 1){
                $cart[$id]--;
            } else {
                unset($cart[$id]);
            }
        }

        $session->set('panier', $cart);

        // Redirection vers la page du panier
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route(path: '/supprimer/{id}', name: 'delete')]
    public function delete(
        Product $product,
        SessionInterface $session
    )
    {
        // Récuperation de l'id du produit
        $id = $product->getId();

        // Récupèration du panier existant
        $cart = $session->get('panier', []);

        // Retirer les produits du panier
        if (!empty($cart[$id])){
            unset($cart[$id]);
        }


        $session->set('panier', $cart);

        // Redirection vers la page du panier
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route(path:'/vider-panier', name: 'emptyCart')]
    public function emptyCart(SessionInterface $session)
    {
        $session->remove('panier');

        return $this->redirectToRoute('app_cart_index');
    }
}