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

final class LoginController extends AbstractController
{


    private UserLoginServiceInterface $loginService;
    private ValidatorInterface $validator;


    // Injection du service d'inscription
    public function __construct(UserLoginServiceInterface $loginService, ValidatorInterface $validator)
    {
        $this->loginService = $loginService;
        $this->validator = $validator;
    }


    #[Route('/login', name: 'app_login')]
    public function login(Request $request): Response
    {
        try {
            $user = new User();
            $form = $this->createForm(UserLoginType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $password = $form->get('password')->getData();
                $success = $this->loginService->login($user, $password);

                if (!$success) {
                    $this->addFlash('error', 'Une erreur est survenue lors de la connexion.');
                    return $this->redirectToRoute('app_login');
                }

                $this->addFlash('success', 'Connexion réussie.');
                return $this->redirectToRoute('app_home_page');

            }

            // // Si le formulaire n'est pas soumis ou est invalide, afficher le formulaire avec les erreurs
            // // Cela inclut aussi les erreurs de formulaire
            // if ($form->isSubmitted() && !$form->isValid()) {
            //     $this->addFlash('error', 'Le formulaire est mal renseigné');
            // }

            // Si le formulaire n'a pas été soumis ou est invalide, affichage de la page avec le formulaire
            return $this->render('login/login.html.twig', [
                'form' => $form->createView()
            ]);

        } catch(\Exception $e) {
            // TODO pages d'erreur 
            // $this->addFlash('error', 'Une erreur est survenue lors de la connexion. Veuillez contacter votre administrateur.');
            // return $this->redirectToRoute('error_page');  // Page d'erreur personnalisée
        }
        
    }
}
