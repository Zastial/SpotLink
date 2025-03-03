<?php

namespace App\Security;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Lcobucci\Clock\SystemClock;
use DateTimeImmutable;
use Exception;
use App\Entity\User;
use App\Constants\AppConst;
use Symfony\Component\HttpFoundation\Request;

/**
 * Classe JwtService permettant de gérer la création et validationdes tokens JWT.
 */
class JwtService
{
    private Configuration $config;

    public function __construct()
    {
        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($_ENV['JWT_SECRET_KEY'])
        );
    }




    /**
     * Récupère le Jwt via le cookie
     */
    public function getJwtTokenFromRequest(Request $request) : ?string {
        $token = $request->cookies->get('Bearer');
        return empty($token) ? null : $token;
    }


    /**
     * Crée un token JWT pour un utilisateur.
     * @param User $user L'utilisateur pour lequel créer le token.
     * @return Plain Le token JWT créé.
     */
    public function createToken(User $user): Plain
    {
        $now = new DateTimeImmutable();
        $expiresAt = $now->modify('+1 hour');

        return $this->config->builder()
            ->issuedBy(AppConst::APP_NAME)
            ->permittedFor(AppConst::APP_NAME)
            ->identifiedBy(bin2hex(random_bytes(16)), true)
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->withClaim('roles', $user->getRole())
            ->expiresAt($expiresAt)
            ->withClaim('uid', $user->getId())
            ->getToken($this->config->signer(), $this->config->signingKey());
    }

    /**
     * Vérifie et parse un token JWT.
     * @param string $token Le token JWT à vérifier.
     * @return Plain|null Le token JWT parsé ou null si le token est invalide.
     */
    public function parseToken(string $token): ?Plain
    {
        try {
            $parsedToken = $this->config->parser()->parse($token);
            if (!$parsedToken instanceof Plain) {
                return null;
            }

            $constraints = [
                new IssuedBy(AppConst::APP_NAME),
                new ValidAt(SystemClock::fromUTC()),
            ];

            if (!$this->config->validator()->validate($parsedToken, ...$constraints)) {
                return null;
            }

            return $parsedToken;
        } catch (Exception $e) {
            return null;
        }
    }
}