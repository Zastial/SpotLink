<?php

namespace App\Services;

use App\Security\JwtService;
use App\Dto\UserDto;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;

/**
 * Service de récupération des informations de l'utilisateur à partir du JWT.
 */
class GetUserInformationService
{
    private JwtService $jwtService;
    private UserRepository $userRepository;

    public function __construct(JwtService $jwtService, UserRepository $userRepository)
    {
        $this->jwtService = $jwtService;
        $this->userRepository = $userRepository;
    }

    
    /**
     * Récupère les informations de l'utilisateur courant.
     */
    public function getUserInformation(Request $request) : ?UserDto
    {
        // Récupérer le token JWT
        $token = $this->jwtService->getJwtTokenFromRequest($request);

        if ($token === null) {
            return null;
        }

        $parsedToken = $this->jwtService->parseToken($token);
        
        $userId = $parsedToken->claims()->get('uid');
        $user = $this->userRepository->find($userId);
        if ($user) {
            $userDto = new UserDto($user);
            return $userDto;
        }

        return null;
    }

}