<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use App\Repository\UserRepository;
use App\Security\JwtService;
use Symfony\Component\Routing\RouterInterface;


/**
 * Classe JwtAuthenticator permettant de gérer l'authentification JWT.
 */
class JwtAuthenticator extends AbstractAuthenticator
{
    private $jwtService;
    private $userRepository;
    private RouterInterface $router;


    /**
     * Constructeur de la classe JwtAuthenticator.
     * @param JwtService $jwtService Service de gestion des jetons JWT.
     * @param UserRepository $userRepository Référence vers le dépôt des utilisateurs.
     * @param RouterInterface $router Interface de gestion des routes.
     */
    public function __construct(JwtService $jwtService, UserRepository $userRepository, RouterInterface $router)
    {
        $this->jwtService = $jwtService;
        $this->userRepository = $userRepository;
        $this->router = $router;
    }

    /**
     * Indique s'il est nécessaire de déclencher l'authentification pour la requête donnée.
     */
    public function supports(Request $request): bool
    {

        // Pas besoin d'authentification pour ces routes
        if (preg_match('#^/(login|register|home|access_denied)#', $request->getPathInfo())) {
            return false;
        }
        return $request->cookies->get('Bearer') ? true : false;

        # Version header Authorization
        // $authHeader = $request->headers->get('Authorization');
        // if (!$authHeader) {
        //     return false;  // Aucun jeton JWT dans l'en-tête Authorization
        // }
        // Vérifier que le jeton commence bien par "Bearer "
        //return preg_match('/^Bearer\s/', $authHeader);
    }

    /**
     * Gère l'authentification JWT pour la requête donnée.
     * Notamment via l'entête Authorization.
     * @throws AuthenticationException
     * @return Passport Retourne un objet Passport contenant les informations d'authentification.
     */
    public function authenticate(Request $request): Passport
    {
        $token = $request->cookies->get('Bearer');  // Récupérer le cookie Bearer

        # Version header Authorization
        //$authHeader = $request->headers->get('Authorization');
        //$token = substr($authHeader, 7);

        $parsedToken = $this->jwtService->parseToken($token);

        if (!$parsedToken) {
            throw new AuthenticationException('Token JWT invalide ou expiré.');
        }
        echo "<script>console.log(' Token exists ');</script>";

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
     * Cas d'un mauvais mot de passe par exemple.
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?JsonResponse
    {

        if ($exception instanceof AuthenticationCredentialsNotFoundException) {
            // Rediriger vers la page de login si le token n'est pas présent
            return new RedirectResponse($this->router->generate('app_login'));
        }

        if ($exception instanceof AccessDeniedException) {
            // Rediriger vers la page d'erreur d'accès
            return new RedirectResponse($this->router->generate('app_access_denied'));
        }

        return new RedirectResponse($this->router->generate('app_login'));


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
     * Appeler quand l'utilisateur n'est pas authentifié.
     */
    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        // Quand le token est absent ou invalide
        return new RedirectResponse($this->router->generate('app_login'));
    }
}
