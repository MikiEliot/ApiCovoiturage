<?php

namespace App\Controller;

use App\Entity\Marque;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/api')]
class MarqueController extends AbstractController
{

    /**
     * @OA\Post(
     *     path="/api/insertMarque/{nom}",
     *     summary="Insérer une marque",
     *     @OA\Parameter(
     *         name="nom",
     *         in="path",
     *         description="Le nom de la marque",
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
     *         response=400,
     *         description="Retourne une erreur si une exception est levée lors de la création de la marque",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/insertMarque/{nom}', name: 'app_insert_marque', methods: ['POST'])]
    public function insertMarque(string $nom, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $marque = new Marque();
            $marque->setNom($nom);

            $entityManager->persist($marque);
            $entityManager->flush();

            return new JsonResponse(['status' => 'Marque cree avec success'], Response::HTTP_CREATED);
        }
        catch (Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la creation de la marque'], Response::HTTP_BAD_REQUEST);
        }

    }

    /**
     * @OA\Delete(
     *     path="/api/deleteMarque/{id}",
     *     summary="Supprimer une marque",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="L'identifiant de la marque",
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
     *         description="Retourne une erreur si la marque n'est pas trouvée",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Retourne une erreur si une exception est levée lors de la suppression de la marque",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/deleteMarque/{id}', name: 'app_delete_marque', methods: ['DELETE'])]
    public function deleteMarque(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $marque = $entityManager->getRepository(Marque::class)->find($id);

            if (!$marque) {
                return new JsonResponse(['error' => 'Marque pas trouve'], Response::HTTP_NOT_FOUND);
            }

            $entityManager->remove($marque);
            $entityManager->flush();

            return new JsonResponse(['status' => 'Marque supprime'], Response::HTTP_OK);
        }
        catch (Exception $e) {
            return new JsonResponse(['error' => 'cette marque est liée a une voiture, suppresion impossible'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/listeMarque",
     *     summary="Lister les marques",
     *     @OA\Response(
     *         response=200,
     *         description="Retourne une liste de marques",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Marque::class))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Retourne une erreur si aucune marque n'est trouvée",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Retourne une erreur si une exception est levée lors de la récupération des marques",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/listeMarque', name: 'app_liste_marque', methods: ['GET'])]
    public function listeMarque(EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $marques = $entityManager->getRepository(Marque::class)->findAll();

            if (!$marques) {
                return new JsonResponse(['error' => 'Marques pas trouve'], Response::HTTP_NOT_FOUND);
            }

            $marquesArray = [];
            foreach ($marques as $marque) {
                $marquesArray[] = [
                    'id' => $marque->getId(),
                    'nom' => $marque->getNom(),
                ];
            }

            return new JsonResponse($marquesArray, Response::HTTP_OK);
        }
        catch (Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la recuperation des marques'], Response::HTTP_BAD_REQUEST);
        }
    }


}
