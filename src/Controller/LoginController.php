<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserLoginType;
use App\Services\Interfaces\UserLoginServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Security\JwtService;
use Symfony\Component\HttpFoundation\Cookie;

final class LoginController extends AbstractController
{


    private UserLoginServiceInterface $loginService;
    private ValidatorInterface $validator;
    private JwtService $jwtService;


    // Injection du service d'inscription
    public function __construct(UserLoginServiceInterface $loginService,
                                ValidatorInterface $validator,
                                JwtService $jwtService)
    {
        $this->loginService = $loginService;
        $this->validator = $validator;
        $this->jwtService = $jwtService;
    }


    #[Route('/login', name: 'app_login')]
    public function login(Request $request): Response
    {
        try {
            $user = new User();
            $form = $this->createForm(UserLoginType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $email = $form->get('email')->getData();
                $password = $form->get('password')->getData();

                $user = $this->loginService->authenticateUser($email, $password);

                if (!$user) {
                    $this->addFlash('error', 'L\'email ou le mot de passe sont incorrects.');
                    return $this->redirectToRoute('app_login');
                }



                // Générer le token JWT pour l'utilisateur
                $token = $this->jwtService->createToken($user);

                // Récupérer la date d'expiration du token
                $expiresAt = $token->claims()->get('exp');

                 // Créer le cookie avec l'option HttpOnly pour la sécurité
                $cookie = Cookie::create('BEARER', $token->toString(), $expiresAt, '/', null, false, true);
                // Ce cookie sera envoyé avec les requêtes de l'utilisateur

                // Rediriger vers la page d'accueil ou le dashboard
                $response = $this->redirectToRoute('app_home_page');
                $response->headers->setCookie($cookie);
                return $response;
            }


               

            // Si le formulaire n'a pas été soumis ou est invalide, affichage de la page avec le formulaire
            return $this->render('login/login.html.twig', [
                'form' => $form->createView()
            ]);

        } catch(\Exception $e) {
            echo "<script>console.log(' Erreur ')</script>";
            echo "<script>console.log('".$e->getMessage()."')</script>";
            // TODO pages d'erreur 
            // $this->addFlash('error', 'Une erreur est survenue lors de la connexion. Veuillez contacter votre administrateur.');
             return $this->redirectToRoute('error_page');  // Page d'erreur personnalisée
        }
    }


    #[Route('/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): Response
    {
        // Symfony gère automatiquement la déconnexion
        // On peut rediriger ou simplement retourner une réponse
        
        // TODO invalider token ?
        return $this->json(['message' => 'Déconnexion réussie.']);
    }
}
