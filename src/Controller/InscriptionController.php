<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Entity\Trajet;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api')]

class InscriptionController extends AbstractController
{
    /**
     * @OA\Delete(
     *     path="/api/deletePersonne/{id}",
     *     summary="Supprimer une personne",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="L'identifiant de l'élève",
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
     *         description="Retourne une erreur si l'élève n'est pas trouvé",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Retourne une erreur si une exception est levée lors de la suppression de l'élève",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/listeInscription', name: 'app_liste_inscription', methods: ['GET'])]
    public function listeInscription(EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $inscriptions = $entityManager->getRepository(Trajet::class)->createQueryBuilder('t')
                ->leftJoin('t.inscriptions', 'i')
                ->addSelect('i')
                ->getQuery()
                ->getResult();

            $data = [];
            foreach ($inscriptions as $inscription) {
                $eleveData = [];
                foreach ($inscription->getInscriptions() as $eleve) {
                    $eleveData[] = [
                        'id' => $eleve->getId(),
                        'prenom' => $eleve->getPrenom(),
                        'nom' => $eleve->getNom(),
                        'telephone' => $eleve->getTelephone(),
                        'email' => $eleve->getEmail(),
                        'ville' => $eleve->getHabiter()->getNom(),
                        'voiture' => $eleve->getVoiture()?->getModele(),
                        'places' => $eleve->getVoiture()?->getPlaces(),
                    ];
                }
                $data[] = [
                    'id' => $inscription->getId(),
                    'trajet' => $inscription->getId(),
                    'conducteur' => [
                        'id' => $inscription->getConducteur()->getId(),
                        'prenom' => $inscription->getConducteur()->getPrenom(),
                        'nom' => $inscription->getConducteur()->getNom(),
                        'email' => $inscription->getConducteur()->getEmail()
                    ],
                    'eleves' => $eleveData
                ];
            }

            return new JsonResponse($data, Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la recuperation des inscriptions: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/deletePersonne/{id}",
     *     summary="Supprimer une personne",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="L'identifiant de l'élève",
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
     *         description="Retourne une erreur si l'élève n'est pas trouvé",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Retourne une erreur si une exception est levée lors de la suppression de l'élève",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/listeInscriptionConducteur/{idtrajet}', name: 'app_liste_inscription_conducteur', methods: ['GET'])]
    public function listeInscriptionConducteur(int $idtrajet, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $trajet = $entityManager->getRepository(Trajet::class)->find($idtrajet);

            if (!$trajet) {
                return new JsonResponse(['error' => 'Trajet non trouve'], Response::HTTP_NOT_FOUND);
            }

            $conducteur = $trajet->getConducteur();

            $conducteurData = [
                'id' => $conducteur->getId(),
                'prenom' => $conducteur->getPrenom(),
                'nom' => $conducteur->getNom(),
                'telephone' => $conducteur->getTelephone(),
                'email' => $conducteur->getEmail(),
                'ville' => $conducteur->getHabiter()->getNom(),
                'voiture' => $conducteur->getVoiture()?->getModele(),
                'places' => $conducteur->getVoiture()?->getPlaces(),
            ];

            return new JsonResponse($conducteurData, Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la recuperation du conducteur: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/listeInscriptionUser/{idpers}",
     *     summary="Lister les trajets d'un élève",
     *     @OA\Parameter(
     *         name="idpers",
     *         in="path",
     *         description="L'identifiant de l'élève",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retourne une liste de trajets pour un élève spécifique",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Trajet::class))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Retourne une erreur si l'élève n'est pas trouvé",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Retourne une erreur si une exception est levée lors de la récupération des trajets",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/listeInscriptionUser/{idpers}', name: 'app_liste_inscription_participant', methods: ['GET'])]
    public function listeInscriptionUser(int $idpers, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $eleve = $entityManager->getRepository(Eleve::class)->find($idpers);

            if (!$eleve) {
                return new JsonResponse(['error' => 'Eleve non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $trajets = $eleve->getParticipations();

            $trajetsArray = [];
            foreach ($trajets as $trajet) {
                $trajetsArray[] = [
                    'id' => $trajet->getId(),
                    'km' => $trajet->getDistance(),
                    'dateT' => $trajet->getDateTrajet()->format('Y-m-d'),
                    'villeD' => $trajet->getVilleDepart()->getNom(),
                    'villeA' => $trajet->getVilleArrivee()->getNom()
                ];
            }

            return new JsonResponse($trajetsArray, Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la récupération des trajets: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/insertInscription/{idpers},{idtrajet}",
     *     summary="Inscrire un élève à un trajet",
     *     @OA\Parameter(
     *         name="idpers",
     *         in="path",
     *         description="L'identifiant de l'élève",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idtrajet",
     *         in="path",
     *         description="L'identifiant du trajet",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Retourne un message de succès lors de l'inscription réussie",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Retourne une erreur si l'élève ou le trajet n'est pas trouvé",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Retourne une erreur si une exception est levée lors de l'inscription",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/insertInscription/{idpers},{idtrajet}', name: 'app_insert_inscription', methods: ['POST'])]
    public function insertInscription(int $idpers, int $idtrajet, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $eleve = $entityManager->getRepository(Eleve::class)->find($idpers);
            $trajet = $entityManager->getRepository(Trajet::class)->find($idtrajet);

            if (!$eleve || !$trajet) {
                return new JsonResponse(['error' => 'Eleve ou trajet non trouve'], Response::HTTP_NOT_FOUND);
            }

            $eleve->addParticipation($trajet);
            $entityManager->persist($eleve);
            $entityManager->flush();

            return new JsonResponse(['status' => 'Inscription créée avec succes'], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la creation de l\'inscription: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/deleteInscription/{id}",
     *     summary="Supprimer une inscription",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="L'identifiant de l'inscription",
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
     *         description="Retourne une erreur si l'inscription n'est pas trouvée",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Retourne une erreur si une exception est levée lors de la suppression de l'inscription",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/deleteInscription/{id}', name: 'app_delete_inscription', methods: ['DELETE'])]
    public function deleteInscription(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $inscription = $entityManager->getRepository(Trajet::class)->find($id);

            if (!$inscription) {
                return new JsonResponse(['error' => 'Inscription non trouvée'], Response::HTTP_NOT_FOUND);
            }

            $entityManager->remove($inscription);
            $entityManager->flush();

            return new JsonResponse(['status' => 'Inscription supprimée avec succès'], Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la suppression de l\'inscription: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }




}
