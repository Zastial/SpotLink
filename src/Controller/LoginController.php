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


    #[Route('/login', name: 'login')]
    public function index(): Response
    {

        $user = new User();

        $form = $this->createForm(UserLoginType::class, $user);

        if ($form->isSubmitted() && $form->isValid()) {

            //Récupération du mot de passe
            $password = $form->get('password')->getData();

            $success = $this->loginService->login($user, $password);

            if (!$success) {
                $this->addFlash('error', 'Une erreur est survenue lors de la connexion.');
                return $this->redirectToRoute('login');
            }

            return $this->redirectToRoute('');

        }

        return $this->render('login/login.html.twig', [
            'controller_name' => 'LoginController',
        ]);
    }
}
