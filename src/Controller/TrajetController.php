<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Entity\Trajet;
use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
#[Route('/api')]
class TrajetController extends AbstractController
{

    /**
     * @OA\Get(
     *     path="/api/listeTrajet",
     *     summary="Lister les trajets",
     *     @OA\Response(
     *         response=200,
     *         description="Retourne une liste de trajets",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Trajet::class))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Retourne une erreur si aucun trajet n'est trouvé",
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
    #[Route('/listeTrajet', name: 'app_liste_trajet', methods: ['GET'])]
    public function listeTrajet(EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $trajets = $entityManager->getRepository(Trajet::class)->findAll();

            if (!$trajets) {
                return new JsonResponse(['error' => 'Aucun trajet trouvé'], Response::HTTP_NOT_FOUND);
            }

            $trajetsArray = [];
            foreach ($trajets as $trajet) {
                $trajetsArray[] = [
                    'id' => $trajet->getId(),
                    'km' => $trajet->getDistance(),
                    'idpers' => $trajet->getConducteur()->getNom(),
                    'dateT' => $trajet->getDateTrajet(),
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
     * @OA\Get(
     *     path="/api/rechercheTrajet/{villeD},{villeA},{dateT}",
     *     summary="Rechercher un trajet",
     *     @OA\Parameter(
     *         name="villeD",
     *         in="path",
     *         description="La ville de départ",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="villeA",
     *         in="path",
     *         description="La ville d'arrivée",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="dateT",
     *         in="path",
     *         description="La date du trajet",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retourne une liste de trajets correspondant à la recherche",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Trajet::class))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Retourne une erreur si aucun trajet n'est trouvé",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Retourne une erreur si une exception est levée lors de la recherche des trajets",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/rechercheTrajet/{villeD},{villeA},{dateT}', name: 'app_liste_recherche', methods: ['GET'])]
    public function rechercheTrajet(string $villeD, string $villeA, string $dateT, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $villeDepart = $entityManager->getRepository(Ville::class)->findOneBy(['nom' => $villeD]);
            $villeArrivee = $entityManager->getRepository(Ville::class)->findOneBy(['nom' => $villeA]);
            $dateTrajet = \DateTime::createFromFormat('Y-m-d', $dateT);

            if (!$villeDepart || !$villeArrivee || !$dateTrajet) {
                return new JsonResponse(['error' => 'Paramètres de recherche invalides'], Response::HTTP_BAD_REQUEST);
            }

            $trajets = $entityManager->getRepository(Trajet::class)->findBy([
                'ville_depart' => $villeDepart,
                'ville_arrivee' => $villeArrivee,
                'date_trajet' => $dateTrajet,
            ]);

            if (!$trajets) {
                return new JsonResponse(['error' => 'Aucun trajet trouvé'], Response::HTTP_NOT_FOUND);
            }

            $trajetsArray = [];
            foreach ($trajets as $trajet) {
                $trajetsArray[] = [
                    'id' => $trajet->getId(),
                    'km' => $trajet->getDistance(),
                    'idpers' => $trajet->getConducteur()->getNom(),
                    'dateT' => $trajet->getDateTrajet()->format('Y-m-d'),
                    'villeD' => $trajet->getVilleDepart()->getNom(),
                    'villeA' => $trajet->getVilleArrivee()->getNom(),
                ];
            }

            return new JsonResponse($trajetsArray, Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la recherche des trajets: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/insertTrajet/{km},{idpers},{dateT},{villeD},{villeA}",
     *     summary="Insérer un trajet",
     *     @OA\Parameter(
     *         name="km",
     *         in="path",
     *         description="La distance du trajet",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idpers",
     *         in="path",
     *         description="L'identifiant du conducteur",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="dateT",
     *         in="path",
     *         description="La date du trajet",
     *         required=true,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="villeD",
     *         in="path",
     *         description="L'identifiant de la ville de départ",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="villeA",
     *         in="path",
     *         description="L'identifiant de la ville d'arrivée",
     *         required=true,
     *         @OA\Schema(type="integer")
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
     *         description="Retourne une erreur si une exception est levée lors de la création du trajet",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/insertTrajet/{km},{idpers},{dateT},{villeD},{villeA},{places}', name: 'app_insert_trajet', methods: ['POST'])]
    public function insertTrajet(int $km, int $idpers, \DateTime $dateT, int $villeD, int $villeA, int $places, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $trajet = new Trajet();
            $conducteur = $entityManager->getRepository(Eleve::class)->find($idpers);
            $villeDepart = $entityManager->getRepository(Ville::class)->findOneBy(['id' => $villeD]);
            $villeArrivee = $entityManager->getRepository(Ville::class)->findOneBy(['id' => $villeA]);
            $dateTrajet = $dateT;
            $trajet->setDistance($km);
            $trajet->setConducteur($conducteur);
            $trajet->setDateTrajet($dateTrajet);
            $trajet->setVilleDepart($villeDepart);
            $trajet->setVilleArrivee($villeArrivee);
            $trajet->setPlaces($places);
            $entityManager->persist($trajet);
            $entityManager->flush();

            return new JsonResponse(['status' => 'Trajet cree avec succes'], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la création du trajet: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @OA\Delete(
     *     path="/api/deleteTrajet/{id}",
     *     summary="Supprimer un trajet",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="L'identifiant du trajet",
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
     *         description="Retourne une erreur si le trajet n'est pas trouvé",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Retourne une erreur si une exception est levée lors de la suppression du trajet",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/deleteTrajet/{id}', name: 'app_delete_trajet', methods: ['DELETE'])]
    public function deleteTrajet(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $trajet = $entityManager->getRepository(Trajet::class)->find($id);

            if (!$trajet) {
                return new JsonResponse(['error' => 'Trajet non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $entityManager->remove($trajet);
            $entityManager->flush();

            return new JsonResponse(['status' => 'Trajet supprime avec succes'], Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la suppression du trajet: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
