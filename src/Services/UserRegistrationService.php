<?php

namespace App\Services;

use App\Entity\User;
use App\Services\Interfaces\UserRegistrationServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Utils\CustomResponse;
use App\Entity\Role;
use App\Repository\RoleRepository;


/**
 * Service de gestion de l'inscription des utilisateurs.
 */
class UserRegistrationService implements UserRegistrationServiceInterface
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private RoleRepository $roleRepository;

    public function __construct(EntityManagerInterface $entityManager,  UserPasswordHasherInterface $passwordHasher, RoleRepository $roleRepository)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Enregistrer un nouvel utilisateur.
     * @param User $user L'utilisateur à enregistrer.
     * @param string $password Le mot de passe de l'utilisateur.
     * @return CustomResponse La réponse de l'opération.
     */
    public function register(User $user, String $password): CustomResponse
    {
        try {
            // Encoder le mot de passe de l'utilisateur
            $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            $user->setRole($this->roleRepository->findOneBy(['name' => 'USER']));

            // Enregistrer l'utilisateur dans la base de données
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Retourner une réponse réussie
            return new CustomResponse(true, 'Utilisateur enregistré avec succès.');

        } catch (\Exception $e) {
            // En cas d'erreur, retourner une réponse d'échec avec le message d'erreur
            return new CustomResponse(false, null, $e->getMessage());
        }
    }
}
