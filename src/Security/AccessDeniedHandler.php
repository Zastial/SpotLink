<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

/**
 * Classe AccessDeniedHandler permettant de gérer les erreurs d'accès refusé.
 */
class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Gère les erreurs d'accès refusé.
     * @param Request $request La requête HTTP.
     * @param AccessDeniedException $accessDeniedException L'exception d'accès refusé.
     * @return RedirectResponse|null Retourne une réponse de redirection.
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        return new RedirectResponse($this->router->generate('app_access_denied'));
    }
}
