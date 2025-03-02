<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use App\Repository\UserRepository;
use App\Security\JwtService;

/**
 * Classe JwtAuthenticator permettant de gérer l'authentification JWT.
 */
class JwtAuthenticator extends AbstractAuthenticator
{
    private $jwtService;
    private $userRepository;

    public function __construct(JwtService $jwtService, UserRepository $userRepository)
    {
        $this->jwtService = $jwtService;
        $this->userRepository = $userRepository;
    }

    /**
     * Indique s'il est nécessaire de déclencher l'authentification pour la requête donnée.
     */
    public function supports(Request $request): bool
    {
        // Pas besoin d'authentification pour ces routes
        if (preg_match('#^/(login|register|home)#', $request->getPathInfo())) {
            return false;
        }
        
        // // Only support if a JWT token exists in the cookie
        // return $request->cookies->has('token');

        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader) {
            return false;  // Aucun jeton JWT dans l'en-tête Authorization
        }

        // Vérifier que le jeton commence bien par "Bearer "
        return preg_match('/^Bearer\s/', $authHeader);
    }

    /**
     * Gère l'authentification JWT pour la requête donnée.
     * Notamment via l'entête Authorization.
     * @throws AuthenticationException
     * @return Passport Retourne un objet Passport contenant les informations d'authentification.
     */
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

    /**
     * Gère les erreurs d'authentification.
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?RedirectResponse
    {
        // Cas où l'utilisateur n'a pas de token JWT
        if ($exception instanceof AuthenticationCredentialsNotFoundException) {
            // Rediriger vers la page de login si le token n'est pas présent
            return new RedirectResponse($this->generateUrl('app_login'));
        }

        // Cas où l'utilisateur n'a pas le rôle nécessaire
        if ($exception instanceof AccessDeniedException) {
            // Rediriger vers la page de login (ou une page d'erreur)
            return new RedirectResponse($this->generateUrl('app_login'));  // Vous pouvez aussi définir une page d'erreur spécifique ici
        }

        // Si l'exception est de type générique, renvoyer une réponse 401 (Non autorisé) avec un message d'erreur
        return new RedirectResponse($this->generateUrl('app_login'));
    }

    /**
     * Gère le succès de l'authentification.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?JsonResponse
    {
        return null;
    }

    /**
     * Gère le cas où l'authentification est requise.
     */
    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        return new JsonResponse(['error' => 'Authentification requise.'], JsonResponse::HTTP_UNAUTHORIZED);
    }
}
