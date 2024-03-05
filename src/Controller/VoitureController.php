<?php

namespace App\Controller;

use App\Entity\Marque;
use App\Entity\Voiture;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api')]

class VoitureController extends AbstractController
{

    /**
     * @OA\Post(
     *     path="/api/insertVoiture/{modele},{places},{marqueId},{immatriculation}",
     *     summary="Insérer une voiture",
     *     @OA\Parameter(
     *         name="modele",
     *         in="path",
     *         description="Le modèle de la voiture",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="places",
     *         in="path",
     *         description="Le nombre de places dans la voiture",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="marqueId",
     *         in="path",
     *         description="L'identifiant de la marque de la voiture",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="immatriculation",
     *         in="path",
     *         description="L'immatriculation de la voiture",
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
     *         description="Retourne une erreur si une exception est levée lors de la création de la voiture",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/insertVoiture/{modele},{places},{marqueId},{immatriculation}', name: 'app_insert_voiture', methods: ['POST'])]
    public function insertVoiture(String $modele, int $places, int $marqueId, string $immatriculation, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
        $marque = $entityManager->getRepository(Marque::class)->find($marqueId);
        if (!$marque) {
            return new JsonResponse(['error' => 'Marque non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $voiture = new Voiture();
        $voiture->setModele($modele);
        $voiture->setPlaces($places);
        $voiture->setAssocier($marque);
        $voiture->setImmatriculation($immatriculation);

        $entityManager->persist($voiture);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Voiture créée avec succès'], Response::HTTP_CREATED);
    }catch (Exception $e) {
        return new JsonResponse(['error' => 'Erreur lors de la création de la voiture'], Response::HTTP_BAD_REQUEST);
    }
    }

    /**
     * @OA\Delete(
     *     path="/api/deleteVoiture/{id}",
     *     summary="Supprimer une voiture",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="L'identifiant de la voiture",
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
     *         description="Retourne une erreur si la voiture n'est pas trouvée",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Retourne une erreur si une exception est levée lors de la suppression de la voiture",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/deleteVoiture/{id}', name: 'app_delete_voiture', methods: ['DELETE'])]
    public function deleteVoiture(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $voiture = $entityManager->getRepository(Voiture::class)->find($id);

            if (!$voiture) {
            return new JsonResponse(['error' => 'Voiture non trouvée'], Response::HTTP_NOT_FOUND);
        }

            $entityManager->remove($voiture);
            $entityManager->flush();

            return new JsonResponse(['status' => 'Voiture supprimée avec succès'], Response::HTTP_OK);
        }catch (Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la suppression de la voiture'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/listeVoiture/",
     *     summary="Liste des voitures",
     *     @OA\Response(
     *         response=200,
     *         description="Retourne un tableau d'objets de voitures",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="modele", type="string"),
     *                 @OA\Property(property="places", type="integer"),
     *                 @OA\Property(property="marque", type="string"),
     *                 @OA\Property(property="immatriculation", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Retourne une erreur si aucune voiture n'est trouvée",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Retourne une erreur si une exception est levée lors de la récupération des voitures",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/listeVoiture/', name: 'app_delete_voiture', methods: ['GET'])]

        public function listeVoiture(EntityManagerInterface $entityManager): JsonResponse
        {
            try {
            $voitures = $entityManager->getRepository(Voiture::class)->findAll();

            if (!$voitures) {
                return new JsonResponse(['error' => 'Aucune voiture trouvée'], Response::HTTP_NOT_FOUND);
            }

            $voituresArray = [];
            foreach ($voitures as $voiture) {
                $voituresArray[] = [
                    'id' => $voiture->getId(),
                    'modele' => $voiture->getModele(),
                    'places' => $voiture->getPlaces(),
                    'marque' => $voiture->getAssocier()?->getNom(),
                    'immatriculation' => $voiture->getImmatriculation(),
                ];
            }

            return new JsonResponse($voituresArray, Response::HTTP_OK);
        }catch (Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la récupération des voitures'], Response::HTTP_BAD_REQUEST);
        }
    }

}
