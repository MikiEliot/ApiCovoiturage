<?php

namespace App\Controller;
use Psr\Log\LoggerInterface;

use App\Entity\Compte;
use App\Entity\Eleve;
use App\Entity\Ville;
use App\Entity\Voiture;
use App\Repository\EleveRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/api')]
class EleveController extends AbstractController
{

    #[Route('/selectPersonne/{id}', name: 'app_select_personne', methods: ['GET'])]
    public function selectPersonne(int $id, EleveRepository $eleves): JsonResponse
    {
        $eleve = $eleves->find($id);

        try {
            if (!$eleve) {
                throw new \Exception('Eleve not found');
            }

            $eleveData = [
                'id' => $eleve->getId(),
                'prenom' => $eleve->getPrenom(),
                'nom' => $eleve->getNom(),
                'telephone' => $eleve->getTelephone(),
                'email' => $eleve->getEmail(),
                'habiter_id' => $eleve->getHabiter()?->getId(),
                'voiture_id' => $eleve->getVoiture()?->getId(),
                'lier_id' => $eleve->getLier()?->getId(),
            ];


            return new JsonResponse($eleveData, Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['error' => "eleve non trouve"], Response::HTTP_BAD_REQUEST);
        }
    }
    #[Route('/listPersonne', name: 'app_eleve_liste', methods: ['GET'])]
    public function listPersonne(EleveRepository $eleveRepository): JsonResponse
    {
        $eleves = $eleveRepository->findAll();

        try {
            if (!$eleves) {
                throw new \Exception('liste des eleves est vide');
            }
            $eleveData = array_map(function (Eleve $eleve) {
                return [
                    'id' => $eleve->getId(),
                    'prenom' => $eleve->getPrenom(),
                    'nom' => $eleve->getNom(),
                    'telephone' => $eleve->getTelephone(),
                    'email' => $eleve->getEmail(),
                    'ville_id' => $eleve->getHabiter()?->getId(),
                    'voiture_id' => $eleve->getVoiture()?->getId(),
                    'compte_id' => $eleve->getLier()?->getId(),

                ];
            }, $eleves);

                return new JsonResponse($eleveData, Response::HTTP_OK);
        } catch (Exception $e) {
                return new JsonResponse(['error' => 'liste des eleves est vide'], Response::HTTP_BAD_REQUEST);
        }

    }
    #[Route('/insertPersonne/{lierId},{prenom},{nom},{tel},{email},{ville},{voiture}', name: 'app_eleve_insert', methods: ['POST'])]
    public function insertPersonne(int $lierId,string $prenom, string $nom, string $tel, string $email, int $ville, int $voiture, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $villeEntity = $entityManager->getRepository(Ville::class)->find($ville);
            $voitureEntity = $entityManager->getRepository(Voiture::class)->find($voiture);
            $compteEntity = $entityManager->getRepository(Compte::class)->find($lierId);


            if (!$villeEntity || !$voitureEntity) {
                throw new Exception('Ville ou voiture non trouvee');
            }

            $eleve = new Eleve();
            $eleve->setLier($compteEntity);
            $eleve->setPrenom($prenom);
            $eleve->setNom($nom);
            $eleve->setTelephone($tel);
            $eleve->setEmail($email);
            $eleve->setHabiter($villeEntity);
            $eleve->setVoiture($voitureEntity);

            $entityManager->persist($eleve);
            $entityManager->flush();


            $data = [
                'message' => 'Vous etes inscrit avec succes',
                'eleve_id' => $eleve->getId(),
            ];

        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de l\'inscription', 'exception' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }
    #[Route('/deletePersonne/{id}', name: 'app_eleve_delete', methods: ['DELETE'])]
    public function deletePersonne(EntityManagerInterface $entityManager, int $id): JsonResponse
{
    $eleve = $entityManager->getRepository(Eleve::class)->find($id);
    try {
        if (!$eleve) {
            throw new Exception('eleve non trouve');
        }
        $entityManager->remove($eleve);
        $entityManager->flush();
        return new JsonResponse(['status' => 'eleve supprime'], Response::HTTP_OK);
    } catch (Exception $e) {
        return new JsonResponse(['error' => 'Erreur lors de la suppression', 'exception' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
    }
}
    #[Route('/updatePersonne/{prenom},{nom},{tel},{email},{ville},{voiture},{modele},{places},{id}', name: 'app_eleve_update', methods: ['PUT'])]
    public function updatePersonne(int $id, string $prenom, string $nom, string $tel, string $email, int $ville, int $voiture, string $modele, int $places ,  EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $eleve = $entityManager->getRepository(Eleve::class)->find($id);
            $villeEntity = $entityManager->getRepository(Ville::class)->find($ville);
            $voitureEntity = $entityManager->getRepository(Voiture::class)->find($voiture);

            if (!$eleve || !$villeEntity || !$voitureEntity) {
                throw new Exception('Eleve ou ville ou voiture non trouvee');
            }

            $eleve->setPrenom($prenom);
            $eleve->setNom($nom);
            $eleve->setTelephone($tel);
            $eleve->setEmail($email);
            $eleve->setHabiter($villeEntity);
            $eleve->setVoiture($voitureEntity);
            $eleve->getVoiture()->setModele($modele);
            $eleve->getVoiture()->setPlaces($places);

            $entityManager->persist($eleve);
            $entityManager->flush();

            $data = [
                'message' => 'Eleve mis a jour',
                'eleve_id' => $eleve->getId(),
            ];

        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la mise a jour', 'exception' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

}
