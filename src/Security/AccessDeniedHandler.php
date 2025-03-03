<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Twig\Environment;
use Psr\Log\LoggerInterface;

/**
 * Classe AccessDeniedHandler permettant de gérer les erreurs d'accès refusé.
 */
class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private Environment $twig;
    private RouterInterface $router;
    private LoggerInterface $logger;

    public function __construct(Environment $twig, RouterInterface $router, LoggerInterface $logger)
    {
        $this->twig = $twig;
        $this->router = $router;
        $this->logger = $logger;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        try {
            $this->logger->error('Access Denied: ' . $accessDeniedException->getMessage());

            $content = $this->twig->render('error/access_denied.html.twig', [
                'message' => $accessDeniedException->getMessage(),
                'exception' => $accessDeniedException
            ]);

            return new Response($content, Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            $this->logger->error('Twig rendering failed: ' . $e->getMessage());

            return new Response(
                "<h1>Erreur</h1><p>Une erreur est survenue en générant la page d\'accès refusé. " . $e ."</p>",
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

}
