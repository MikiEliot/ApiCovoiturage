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
