<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profil', name: 'app_profile_')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('profile/list.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }

    #[Route('/commandes', name: 'app_orders')]
    public function orders(): Response
    {
        return $this->render('profile/list.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }
}
