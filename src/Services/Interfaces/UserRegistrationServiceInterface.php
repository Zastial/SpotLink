<?php 

namespace App\Services\Interfaces;

use App\Entity\User;

/**
 * Service qui permet d'inscrire un utilisateur
 */
interface UserRegistrationServiceInterface 
{
    /**
     * Enregistre un utilisateur
     */
    public function register(User $user): bool;
}