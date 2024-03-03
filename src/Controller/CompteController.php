<?php

namespace App\Controller;

use App\Entity\Compte;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Security\Token;

class CompteController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    #[Route('/api/login/{login},{password}', name: 'app_login', methods: ['POST'])]
    public function login(string $login, string $password, EntityManagerInterface $entityManager, Token $token) : JsonResponse
    {
        try{
            // on recupere le compte correspondant au login
            $compte = $entityManager->getRepository(Compte::class)->findOneBy(['login' => $login]);
            // si le compte n'existe pas, on retourne une erreur
            if (!$compte) {
                return $this->json([
                    'error' => 'Aucun compte avec ce login',
                ]);
            }

            // Check if the provided password is valid
            if (!$this->passwordHasher->isPasswordValid($compte, $password)) {
                return $this->json([
                    'error' => 'Mot de passe incorrect',
                ]);
            }

            $token = $token->generateToken($compte);
            $expirationTime = (new \DateTime())->add(new \DateInterval('PT' . $this->getParameter('lexik_jwt_authentication.token_ttl') . 'S'));

            $resultat = [
                'message' => 'Connexion reussie',
                'token' => $token,
                'expiration' => $expirationTime->format('Y-m-d H:i:s')
            ];
        }
        catch (Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la connexion'], Response::HTTP_BAD_REQUEST);
        }
        return new JsonResponse($resultat, Response::HTTP_OK);
    }


    #[Route('/api/register/{login},{password}', name: 'app_register', methods: ['POST'])]
    public function register(string $login, string $password, EntityManagerInterface $entityManager): JsonResponse
    {
        try{
            $utilisateurExistant = $entityManager->getRepository(Compte::class)->findOneBy(['login' => $login]);
            // verifier si le compte existe deja, si oui on retourne une erreur
            if ($utilisateurExistant) {
                return $this->json([
                    'error' => 'Un compte avec ce login existe deja',]);
            }
            // si le compte n'existe pas, on le crée
            $compte = new Compte();
            $compte->setLogin($login);
            $compte->setPassword($this->passwordHasher->hashPassword($compte, $password));
            // on attribue le role ROLE_USER par defaut
            $compte->setRoles(['ROLE_USER']);
            // on persiste le compte
            $entityManager->persist($compte);
            $entityManager->flush();

            $resultat = [
                'message' => 'Compte cree avec succes',
            ];
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la creation du compte', 'exception' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        return new JsonResponse($resultat, Response::HTTP_CREATED);
    }



}

