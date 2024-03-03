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
