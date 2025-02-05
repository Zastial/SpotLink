<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserInscriptionType;
use App\Services\Interfaces\UserRegistrationServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;


final class RegistrationController extends AbstractController
{
    private UserRegistrationServiceInterface $registrationService;
    private ValidatorInterface $validator;


    // Injection du service d'inscription
    public function __construct(UserRegistrationServiceInterface $registrationService, ValidatorInterface $validator)
    {
        $this->registrationService = $registrationService;
        $this->validator = $validator;
    }


    #[Route('/register', name: 'register')]
    public function register(Request $request): Response
    {
        $user = new User();

        $form = $this->createForm(UserInscriptionType::class, $user);

        // Traiter la soumission du formulaire

        if ($form->isSubmitted() && $form->isValid()) {

            //Récupération du mot de passe (mapped à false dans le UserInscriptionType pour le gérer nous même)
            $password = $form->get('password')->getData();

            $success = $this->registrationService->register($user, $password);

            if (!$success) {
                // Afficher un message d'erreur si l'inscription a échoué
                $this->addFlash('error', 'Une erreur est survenue lors de l\'inscription.');
                return $this->redirectToRoute('register'); // Redirige vers la page d'inscription en cas d'échec
            }

            // Si l'inscription a réussi, rediriger vers la page de login
            return $this->redirectToRoute('login');

        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
