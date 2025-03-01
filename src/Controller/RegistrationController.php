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
use App\Utils\CustomResponse;

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
                $response = $this->registrationService->register($user, $password);
    
                if (!$response->success) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'inscription : ' . $response->message);
                    return $this->redirectToRoute('register');
                }
    
                $this->addFlash('success', 'Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.');
                return $this->redirectToRoute('app_login');
            }
    
            // Si le formulaire n'est pas valide, les erreurs seront automatiquement attachées au formulaire
            return $this->render('registration/register.html.twig', [
                'form' => $form->createView(),
            ]);
            
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue. Veuillez réessayer.');
            return $this->redirectToRoute('register');
        }
    }
    
}
