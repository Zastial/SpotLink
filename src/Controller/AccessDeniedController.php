<?php





namespace App\Controller;

use App\Entity\User;
use App\Form\UserLoginType;
use App\Services\Interfaces\UserLoginServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Security\JwtService;
use Symfony\Component\HttpFoundation\Cookie;


/**
 * Classe LoginController permettant de rediriger l'utilisateur vers une 
 * page indiquant que l'utilisateur n'a pas les droit nécessaires pour accéder à la page.
 */
final class AccessDeniedController extends AbstractController
{

    public function __construct()
    {

    }

    /**
     * Redirige l'utilisateur vers une page indiquant que l'utilisateur n'a pas les droit nécessaires pour accéder à la page.
     * @return Response Retourne une réponse HTTP.
     */
    #[Route('/access-denied', name: 'app_access_denied')]
    public function accessDenied(): Response
    {
        return $this->render('error/access_denied.html.twig');
    }
}