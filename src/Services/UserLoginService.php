<?php

namespace App\Services;

use App\Entity\User;
use App\Services\Interfaces\UserLoginServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserLoginService implements UserLoginServiceInterface
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {

        $this->entityManager = $entityManager;
    }

    public function login(User $user, String $password): bool
    {
       try {
            
            //TODO  


            return true;
        } catch (\Exception $e) {
            // Gérer les erreurs ici si besoin
            return false;
        }
    }
}