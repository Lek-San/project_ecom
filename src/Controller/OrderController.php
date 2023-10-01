<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commende', name: 'app_order_')]
class OrderController extends AbstractController
{
    #[Route('/ajout', name: 'add')]
    public function addOrder(
        SessionInterface$session,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $cart = $session->get('panier', []);

        // Si panier, création de la commande
        $order = new Order();

        // Remplir la commande
        $order->setUsers($this->getUser());
        $order->setReference(uniqid());

        // Parcourrir le panier pour créer les détails de commande
        foreach ($cart as $item => $quantity){
            $orderDetail = new OrderDetail();
            // En premièr chercher le produit
            $product = $productRepository->find($item);
            $price = $product->getPrice();

            // Création du détail de la commande
            $orderDetail->setProducts($product);
            $orderDetail->setPrice($price);
            $orderDetail->setQuantity($quantity);

            $order->addOrderDetail($orderDetail);
        }

        $entityManager->persist($order);
        $entityManager->flush();

        $session->remove('panier');

        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }
}
