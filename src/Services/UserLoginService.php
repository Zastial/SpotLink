<?php

namespace App\Services;

use App\Entity\User;
use App\Services\Interfaces\UserLoginServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


/**
 * Service de gestion de l'authentification des utilisateurs.
 */
class UserLoginService implements UserLoginServiceInterface
{

    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface  $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, 
                                UserPasswordHasherInterface  $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Authentifier un utilisateur par son email et mot de passe.
     * @param string $email L'email de l'utilisateur.
     * @param string $password Le mot de passe de l'utilisateur.
     * @return User|null L'utilisateur authentifié ou null s'il n'est pas authentifié.
     */
    public function authenticateUser(string $email, string $password): ?User
    {
        // Chercher l'utilisateur par son email
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        // Si l'utilisateur n'existe pas ou si le mot de passe est incorrect
        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            return null;  // L'utilisateur ou mot de passe est incorrect
        }

        return $user;  // Retourner l'utilisateur s'il est authentifié
    }

}