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
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Constraints as Assert;


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
                $confirmPassword = $form->get('confirmPassword')->getData();

                if ($this->passwordAreIncorrect($password, $confirmPassword, $form)) {
                    $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
                    return $this->redirectToRoute('register');
                }
    
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
    

    /**
     * Valide les mots de passe du formulaire d'inscription.
     * @param string $password Le mot de passe à valider.
     * @param string $confirmPassword Le mot de passe de confirmation.
     * @param FormInterface $form Le formulaire d'inscription.
     * @return bool Vrai si les mots de passe sont valides, faux sinon.
     */
    private function passwordAreIncorrect(string $password, string $confirmPassword, Form $form): bool {

        // Valider le mot de passe
        $violations = $this->validator->validate($password, [
            new Assert\Length(['min' => 6, 'minMessage' => 'Le mot de passe doit contenir au moins 6 caractères.']),
            new Assert\NotBlank(['message' => 'Veuillez entrer un mot de passe.']),
        ]);

        // Si des violations existent, les ajouter à l'objet du formulaire
        foreach ($violations as $violation) {
            $form->get('password')->addError(new FormError($violation->getMessage()));
        }

        // Si les mots de passe ne correspondent pas
        if ($password !== $confirmPassword) {
            $form->get('confirmPassword')->addError(new FormError('Les mots de passe ne correspondent pas.'));
        }

        return count($violations) > 0 && $password !== $confirmPassword;
    }
}
