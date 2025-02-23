<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegisterType;
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
    public function __construct(UserRegistrationServiceInterface $registrationService,
                                ValidatorInterface $validator)
    {
        $this->registrationService = $registrationService;
        $this->validator = $validator;
    }


    #[Route('/register', name: 'register')]
    public function register(Request $request): Response
    {
        try {
            $user = new User();
            $form = $this->createForm(UserRegisterType::class, $user);
            
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $password = $form->get('password')->getData();
    
                // Sauvegarde de l'utilisateur en base de données
                $this->registrationService->register($user, $password);
                
                // Ajout d'un message flash pour informer l'utilisateur
                $this->addFlash('success', 'Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.');
    
                return $this->redirectToRoute('login');
            }
        } catch(\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la création de votre compte. Veuillez réessayer.');

            // Si tu veux retourner un message spécifique dans la vue
            return $this->render('registration/register.html.twig', [
                'form' => $form->createView(),
                'error_message' => 'Une erreur est survenue, veuillez réessayer.'
            ]);
        }
       
        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
