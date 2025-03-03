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
use App\Services\GetUserInformationService;
use App\Mapper\UserMapper;
use Doctrine\ORM\EntityManagerInterface;


final class UserInformationsController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private GetUserInformationService $getUserInformationService;
    private UserMapper $userMapper;

    public function __construct(EntityManagerInterface $entityManager, GetUserInformationService $getUserInformationService, UserMapper $userMapper)
    {
        $this->entityManager = $entityManager;
        $this->getUserInformationService = $getUserInformationService;
        $this->userMapper = $userMapper;
    }


    //Affichage de la vue user_informations
    #[Route('/user_informations', name: 'user_informations')]
    public function user_informations(Request $request): Response
    {
        
        $userDto = $this->getUserInformationService->getUserInformation($request);

        if ($userDto === null) {
            #TODO : Remplacer par une notif utilisateur
            throw new \Exception("L'utilisateur est introuvable");
        }

        $user = $this->entityManager->getRepository(User::class)->find($userDto->id);

        $userform = $this->createForm(UserInformationsFormType::class, $userDto);

        $userform->handleRequest($request);
        if ($userform->isSubmitted() && $userform->isValid()) {
           
            $userToUpdate = $this->userMapper->mapUserDtoToUser($userDto, $user);
            
            $entityManager->persist($userToUpdate);
            $entityManager->flush();

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
        if (!$user) {
            #TODO : Remplacer par une notif utilisateur
            throw new \Exception("Le compte est introuvable");
        }
        $userRepository->delete($user);
        return $this->redirectToRoute('app_home_page');
    }

    //Changement du mot de passe utilisateur
    #[Route('/change_password', name: 'change_password', methods: ['POST'])]
    public function change_password(Request $request, UserRepository $userRepository): Response
    {
        #TODO : Remplacer par user connected + hasher password
        $data = $request->request->all();
        $plainPassword = $data['password_field'];
        $user = $userRepository->findOneBy([], ['id' => 'ASC']);

        if (!$this->isCsrfTokenValid('change_password', $request->request->get('_token'))) {
            #TODO : Remplacer par une notif utilisateur
            throw new \Exception('Le CSRF token est invalide');
        }
        if (!$user) {
            #TODO : Remplacer par une notif utilisateur
            throw new \Exception("L'utilisateur est introuvable");
        }else{
            $user->setPassword($plainPassword);
            $userRepository->save($user);
        }
        return $this->redirectToRoute('user_informations');
    }
}
