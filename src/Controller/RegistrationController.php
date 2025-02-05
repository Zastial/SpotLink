<?php

namespace App\Controller;

use App\Services\Interfaces\UserRegistrationServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RegistrationController extends AbstractController
{


     // Injection du service d'inscription
    public function __construct(UserRegistrationServiceInterface $registrationService)
    {
        $this->registrationService = $registrationService;
    }


    #[Route('/register', name: 'rsegister')]
    public function register(Request $request): Response
    {
        $user = new User(); // Créer une nouvelle instance de User

        // Créer le formulaire d'inscription
        $form = $this->createForm(UserType::class, $user);

        // Traiter la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            
            // Validation des données (les annotations @Assert sont vérifiées ici)
            $errors = $validator->validate($user);

            if (count($errors) > 0) {
                // Si des erreurs sont trouvées, les afficher (elles seront envoyées à la vue)
                return $this->render('registration/register.html.twig', [
                    'form' => $form->createView(),
                    'errors' => $errors,
                ]);
            }


            // Utilisation du service d'enregistrement
            $success = $this->registrationService->register($user);

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
