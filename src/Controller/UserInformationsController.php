<?php

namespace App\Controller;

use App\Form\UserInformationsFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Entity\User;

final class UserInformationsController extends AbstractController
{
    #[Route('/user_informations', name: 'user_informations')]
    public function user_informations(Request $request, UserRepository $userRepository): Response
    {
        #TODO : Remplacer par user connected
        $user = $userRepository->findOneBy([], ['id' => 'ASC']);
        $form = $this->createForm(UserInformationsFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user);
        }

        return $this->render('user_informations/userInformations.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete_account', name: 'delete_account')]
    public function delete_account(Request $request, UserRepository $userRepository): Response
    {
        #TODO : Remplacer par user connected
        $user = $userRepository->findOneBy([], ['id' => 'ASC']);
        $userRepository->delete($user);
        return $this->redirectToRoute('app_home_page');
    }
    
}
