<?php
namespace App\Services;

use App\Entity\User;
use App\Services\Interfaces\UserRegistrationServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Utils\CustomResponse;

class UserRegistrationService implements UserRegistrationServiceInterface
{

    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager,  UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }


    public function register(User $user, String $password): CustomResponse
    {
        try {
            //TODO : à faire une fois les pages faites et au moment de l'utilisation du JWT

            // Encoder le mot de passe de l'utilisateur
            // Hash du mot de passe avant de l'enregistrer en base
            $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setIsVerify(false);

            // Enregistrer l'utilisateur dans la base de données
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Retourner vrai si tout s'est bien passé
            return new CustomResponse(true,);
        } catch (\Exception $e) {
            // Gérer les erreurs ici si besoin
            return new CustomResponse(false, null, $e->getMessage());
        }
    }
}