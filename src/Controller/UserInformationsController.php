<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\UserInformationsFormType;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Entity\User;
use function PHPUnit\Framework\throwException;

final class UserInformationsController extends AbstractController
{
    //Affichage de la vue user_informations
    #[Route('/user_informations', name: 'user_informations')]
    public function user_informations(Request $request, UserRepository $userRepository): Response
    {
        #TODO : Remplacer par user connected
        $user = $userRepository->findOneBy([], ['id' => 'ASC']);
        $userform = $this->createForm(UserInformationsFormType::class, $user);

        $userform->handleRequest($request);
        if ($userform->isSubmitted() && $userform->isValid()) {
            try{
                if (!$user){
                    throw new \Exception('Un problème est survenu lors de l\'opération.');
                }
                $userRepository->save($user);
                $this->addFlash('success', "Vos informations ont été mises à jour !");

            }catch(\Exception $e){
                $this->addFlash('error', "Un problème est survenu lors de l'opération.");
                return $this->redirectToRoute("user_informations");
            }
        }
        return $this->render('user_informations/userInformations.html.twig', [
            'userform' => $userform->createView(),
        ]);
    }

    //Suppression du compte de l'utilisateur
    #[Route('/delete_account', name: 'delete_account')]
    public function delete_account(Request $request, UserRepository $userRepository): Response
    {
        #TODO : Remplacer par user connected
        $user = $userRepository->findOneBy([], ['id' => 'ASC']);
        try{
            $userRepository->delete($user);
            $this->addFlash('success', "Votre compte a été supprimé avec succès !");

            #TODO : Enlever les routes pour les users connectés
            return $this->redirectToRoute('app_home_page');
        }catch(\Exception $e){
            $this->addFlash('error', "Un problème est survenu lors de l'opération.");
            return $this->redirectToRoute("user_informations");
        }
    }

    //Changement du mot de passe utilisateur
    #[Route('/change_password', name: 'change_password', methods: ['POST'])]
    public function change_password(Request $request, UserRepository $userRepository): Response
    {
        #TODO : Remplacer par user connected + hasher password
        $data = $request->request->all();
        $plainPassword = $data['password_field'];
        $user = $userRepository->findOneBy([], ['id' => 'ASC']);

        try{
            if (!$this->isCsrfTokenValid('change_password', $request->request->get('_token'))) {
                $this->addFlash('error', "Le CSRF token est invalide");
            }
            if (!$user){
                throw new \Exception('Un problème est survenu lors de l\'opération.');
            }
            $user->setPassword($plainPassword);
            $userRepository->save($user);
            $this->addFlash('success', "Votre mot de pass a été modifié avec succès !");
            return $this->redirectToRoute('user_informations');
        }catch(\Exception $e){
            $this->addFlash('error', "Un problème est survenu lors de l'opération.");
            return $this->redirectToRoute("user_informations");
        }
    }
}
