<?php

namespace App\Security;

use App\Repository\UserRepository;
use App\Security\JwtService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class JwtAuthenticator extends AbstractAuthenticator
{
    private $jwtService;
    private $userRepository;

    public function __construct(JwtService $jwtService, UserRepository $userRepository)
    {
        $this->jwtService = $jwtService;
        $this->userRepository = $userRepository;
    }

    public function supports(Request $request): bool
    {
        if (preg_match('#^/(login|register)#', $request->getPathInfo())) {
            return false;
        }
        
        // Only support if a JWT token exists in the cookie
        return $request->cookies->has('token');
    }

    public function authenticate(Request $request): Passport
    {
        $authHeader = $request->headers->get('Authorization');
        $token = substr($authHeader, 7);

        $parsedToken = $this->jwtService->parseToken($token);

        if (!$parsedToken) {
            throw new AuthenticationException('Token JWT invalide ou expiré.');
        }

        $userId = $parsedToken->claims()->get('uid');
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new AuthenticationException('Utilisateur non trouvé.');
        }

        return new SelfValidatingPassport(new UserBadge($userId, function () use ($user) {
            return $user;
        }));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?JsonResponse
    {
        return new JsonResponse(['error' => $exception->getMessage()], JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?JsonResponse
    {
        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        return new JsonResponse(['error' => 'Authentification requise.'], JsonResponse::HTTP_UNAUTHORIZED);
    }
}