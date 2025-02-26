<?php

namespace App\Controller;

use App\Form\UserInformationsFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

final class UserInformationsController extends AbstractController
{
    #[Route('/user_informations', name: 'user_informations')]
    public function user_informations(Request $request,): Response
    {
        $event = new User();
        $form = $this->createForm(UserInformationsFormType::class, $event);
        $form->handleRequest($request);

        return $this->render('user_informations/userInformations.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
