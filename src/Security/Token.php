<?php

namespace App\Security;


use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
class Token
{
    private JWTTokenManagerInterface $JWTManager;
    // constructeur de la classe Token qui prend en paramètre un objet de type JWTTokenManagerInterface
    public function __construct(JWTTokenManagerInterface $JWTManager)
    {
        $this->JWTManager = $JWTManager;
    }

    // méthode qui génère un token pour un utilisateur
    public function generateToken(UserInterface $user): string
    {
        return $this->JWTManager->create($user);
    }

}