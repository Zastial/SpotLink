<?php
namespace App\Services;

use App\Entity\User;
use App\Services\Interfaces\UserRegistrationServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserRegistrationService implements UserRegistrationServiceInterface
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {

        $this->entityManager = $entityManager;
    }


    public function register(User $user): bool
    {
        try {
            // Encoder le mot de passe de l'utilisateur
            
            // Enregistrer l'utilisateur dans la base de données
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Retourner vrai si tout s'est bien passé
            return true;
        } catch (\Exception $e) {
            // Gérer les erreurs ici si besoin
            return false;
        }
    }
}