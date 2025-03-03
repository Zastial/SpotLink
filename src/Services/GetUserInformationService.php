<?php

namespace App\Services;

use App\Security\JwtService;

/**
 * Service de récupération des informations de l'utilisateur à partir du JWT.
 */
class GetUserInformationService
{
    public function __construct(JwtService $jwtService)
    {
    }

    /**
     * Récupère les informations de l'utilisateur courant.
     */
    public function getUserInformation() : ?User
    {
        // Récupérer le token JWT
        $token = $this->jwtService->getJwtToken();

        if ($token === null) {
            return null;
        }

        $parsedToken = $this->jwtService->parseToken($token);
        
        $userId = $parsedToken->claims()->get('uid');
        $user = $this->userRepository->find($userId);

        return $user;
    }

}