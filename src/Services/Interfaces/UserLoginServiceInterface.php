<?php 

namespace App\Services\Interfaces;

use App\Entity\User;

/**
 * Service qui permet de connecter un utilisateur
 */
interface UserLoginServiceInterface 
{
    /**
     * Connecte un utilisateur
     */
    public function authenticateUser(string $email, String $password): ?User;
}