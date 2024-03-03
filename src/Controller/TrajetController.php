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

    #[Route('/insertTrajet/{km},{idpers},{dateT},{villeD},{villeA}', name: 'app_insert_trajet', methods: ['POST'])]
    public function insertTrajet(int $km, int $idpers, \DateTime $dateT, int $villeD, int $villeA, EntityManagerInterface $entityManager): JsonResponse
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
            $entityManager->persist($trajet);
            $entityManager->flush();

            return new JsonResponse(['status' => 'Trajet cree avec succes'], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la création du trajet: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

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
