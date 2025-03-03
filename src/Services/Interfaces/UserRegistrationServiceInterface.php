<?php 

namespace App\Services\Interfaces;

use App\Entity\User;
use App\Utils\CustomResponse;

/**
 * Service qui permet d'inscrire un utilisateur
 */
interface UserRegistrationServiceInterface 
{
    /**
     * Enregistre un utilisateur
     */
    public function register(User $user, String $password): CustomResponse;
}