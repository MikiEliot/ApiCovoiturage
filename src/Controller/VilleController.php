<?php

namespace App\Controller;

use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
#[Route('/api')]

class VilleController extends AbstractController
{

    /**
     * @OA\Post(
     *     path="/api/insertVille/{nom},{cp}",
     *     summary="Insérer une ville",
     *     @OA\Parameter(
     *         name="nom",
     *         in="path",
     *         description="Le nom de la ville",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="cp",
     *         in="path",
     *         description="Le code postal de la ville",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Retourne un message de succès lors de la création réussie",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Retourne une erreur si une exception est levée lors de la création de la ville",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/insertVille/{nom},{cp}', name: 'app_insert_ville', methods: ['POST'])]
    public function insertVille(string $nom, string $cp, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $ville = new Ville();
            $ville->setNom($nom);
            $ville->setCp($cp);

            $entityManager->persist($ville);
            $entityManager->flush();

            return new JsonResponse(['status' => 'Ville creee avec succès'], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la création de la ville: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/deleteVille/{id}",
     *     summary="Supprimer une ville",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="L'identifiant de la ville",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retourne un message de succès lors de la suppression réussie",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Retourne une erreur si la ville n'est pas trouvée",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Retourne une erreur si une exception est levée lors de la suppression de la ville",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/deleteVille/{id}', name: 'app_delete_ville', methods: ['DELETE'])]
    public function deleteVille(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $ville = $entityManager->getRepository(Ville::class)->find($id);

            if (!$ville) {
                return new JsonResponse(['error' => 'Ville non trouvee'], Response::HTTP_NOT_FOUND);
            }

            $entityManager->remove($ville);
            $entityManager->flush();

            return new JsonResponse(['status' => 'Ville supprimee avec succes'], Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la suppression de la ville: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/listeVille",
     *     summary="Lister les villes",
     *     @OA\Response(
     *         response=200,
     *         description="Retourne une liste de villes",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Ville::class))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Retourne une erreur si aucune ville n'est trouvée",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Retourne une erreur si une exception est levée lors de la récupération des villes",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/listeVille', name: 'app_liste_ville', methods: ['GET'])]
    public function listeVille(EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $villes = $entityManager->getRepository(Ville::class)->findAll();

            if (!$villes) {
                return new JsonResponse(['error' => 'Aucune ville trouvee'], Response::HTTP_NOT_FOUND);
            }

            $villesArray = [];
            foreach ($villes as $ville) {
                $villesArray[] = [
                    'id' => $ville->getId(),
                    'nom' => $ville->getNom(),
                ];
            }

            return new JsonResponse($villesArray, Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la recuperation des villes: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
