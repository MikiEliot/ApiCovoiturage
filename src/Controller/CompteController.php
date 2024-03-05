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
    /**
     * @OA\Post(
     *     path="/api/login/{login},{password}",
     *     summary="Connecter un utilisateur",
     *     @OA\Parameter(
     *         name="login",
     *         in="path",
     *         description="Le login de l'utilisateur",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="path",
     *         description="Le mot de passe de l'utilisateur",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retourne un token lors de la connexion réussie",
     *         @OA\JsonContent(ref=@Model(type=Compte::class))
     *     )
     * )
     */
    #[Route('/api/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request, EntityManagerInterface $entityManager, Token $token) : JsonResponse
    {
        try{
            $data = json_decode($request->getContent(), true);
            $login = $data['login'];
            $password = $data['password'];

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

    /**
     * @OA\Post(
     *     path="/api/register/{login},{password}",
     *     summary="Enregistrer un nouvel utilisateur",
     *     @OA\Parameter(
     *         name="login",
     *         in="path",
     *         description="Le login du nouvel utilisateur",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="path",
     *         description="Le mot de passe du nouvel utilisateur",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Retourne un message de succès lors de l'enregistrement réussi",
     *         @OA\JsonContent(ref=@Model(type=Compte::class))
     *     )
     * )
     */
    #[Route('/api/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try{
            $login = $data['login'];
            $password = $data['password'];
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

