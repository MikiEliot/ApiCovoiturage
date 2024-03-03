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
