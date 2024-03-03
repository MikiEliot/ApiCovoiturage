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


class InscriptionController extends AbstractController
{
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
