<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use App\Service\JWTservice;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        UserAuthenticator $authenticator,
        EntityManagerInterface $entityManager,
        SendMailService $mail,
        JWTservice $jwt
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            //Génération du token JWT de l'utilisateur
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            $payload = [
                'user_id' => $user->getId()
            ];

            $token = $jwt->generateToken($header, $payload, $this->getParameter('app.jwtsecret'));
//            dd($token);

            //Envoie de l'email
            $mail->send(
                'no-reply@monsite.net',
                $user->getEmail(),
                'Activation de votre compte sur notre site E-COM',
                'register',
                compact('user', 'token')
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verification/{token}', name: 'verify_user')]
    public function verifyUser(
        string $token,
        JWTservice $jwt,
        UserRepository $userRepository,
        EntityManagerInterface $emi
    ): Response
    {
//        dd($jwt->check($token, $this->getParameter('app.jwtsecret')));
        //Vérifier si le token est valide, si il n'a pas expiré et n'a pas était modifié
        if ($jwt->isValide($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))){
            //Récupèrer le Payload
            $payload = $jwt->getPayload($token);
            //Récupèrer le User du token
            $user = $userRepository->find($payload['user_id']);
            //Verifier que l'utilisateur existe et n'a pas encore activé son compte
            if($user && !$user->getIsVerified()){
                $user->setIsVerified(true);
                $emi->flush($user);
                $this->addFlash('success', 'Utilisateur Activé');
                return $this->redirectToRoute('app_profile_index');

            }
        }

        $this->addFlash('denger', 'Le token est invalide ou expiré');

        return $this->redirectToRoute('app_login');
    }

    #[Route('/renvoi/verification', name: 'resend_verification_user')]
    public function resendVerificationUser(JWTservice $jwt, SendMailService $mail, UserRepository $userRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }

        if ($user->getIsVerified()){
            $this->addFlash('warning', 'Cet utilisateur est déjà activé');
            return $this->redirectToRoute('app_profile_index');
        }

        //Génération du token JWT de l'utilisateur
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        $payload = [
            'user_id' => $user->getId()
        ];

        $token = $jwt->generateToken($header, $payload, $this->getParameter('app.jwtsecret'));

        //Envoie de l'email
        $mail->send(
            'no-reply@monsite.net',
            $user->getEmail(),
            'Activation de votre compte sur notre site E-COM',
            'register',
            compact('user', 'token')
        );

        $this->addFlash('success', 'Email de verification envoyé');
        return $this->redirectToRoute('app_profile_index');
    }
}
