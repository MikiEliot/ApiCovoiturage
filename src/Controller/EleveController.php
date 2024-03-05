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
    /**
     * @OA\Get(
     *     path="/api/selectPersonne/{id}",
     *     summary="Sélectionner une personne",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="L'identifiant de l'élève",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retourne les détails de l'élève lors de la sélection réussie",
     *         @OA\JsonContent(ref=@Model(type=Eleve::class))
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
     *         description="Retourne une erreur si une exception est levée lors de la récupération des détails de l'élève",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/listPersonne",
     *     summary="Lister les personnes",
     *     @OA\Response(
     *         response=200,
     *         description="Retourne une liste de personnes",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Eleve::class))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Retourne une erreur si aucune personne n'est trouvée",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Retourne une erreur si une exception est levée lors de la récupération des personnes",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */

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


    /**
     * @OA\Post(
     *     path="/api/insertPersonne/{lierId},{prenom},{nom},{tel},{email},{ville},{voiture}",
     *     summary="Inscrire une personne",
     *     @OA\Parameter(
     *         name="lierId",
     *         in="path",
     *         description="L'identifiant du compte lié",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="prenom",
     *         in="path",
     *         description="Le prénom de l'élève",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="nom",
     *         in="path",
     *         description="Le nom de l'élève",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="tel",
     *         in="path",
     *         description="Le téléphone de l'élève",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         description="L'email de l'élève",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="ville",
     *         in="path",
     *         description="L'identifiant de la ville de l'élève",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="voiture",
     *         in="path",
     *         description="L'identifiant de la voiture de l'élève",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retourne un message de succès lors de l'inscription réussie",
     *         @OA\JsonContent(ref=@Model(type=Eleve::class))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Retourne une erreur si une exception est levée lors de l'inscription",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
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
