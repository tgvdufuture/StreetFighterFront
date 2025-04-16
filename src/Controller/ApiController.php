<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CharacterRepository;
use App\Entity\Character;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Entity\User;

/**
 * @method User getUser()
 */
class ApiController extends AbstractController
{
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    #[Route('/api/characters', name: 'api_characters', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getAllCharacters(CharacterRepository $characterRepository): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $characters = $characterRepository->findBy(['user' => $user]);
        $data = array_map(fn($character) => [
            'id' => $character->getId(),
            'name' => $character->getName(),
            'strength' => $character->getStrength(),
            'speed' => $character->getSpeed(),
            'durability' => $character->getDurability(),
            'power' => $character->getPower(),
            'combat' => $character->getCombat(),
            'user' => $character->getUser()?->getId(),
            'image' => $character->getImage()
        ], $characters);

        return $this->json($data);
    }

    #[Route('/api/characters', name: 'api_characters_options', methods: ['OPTIONS'])]
    public function optionsCharacters(): JsonResponse
    {
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/api/characters/{id}', name: 'api_character_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getCharacterById(int $id, CharacterRepository $characterRepository): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $character = $characterRepository->find($id);
        if (!$character) {
            return $this->json(['error' => 'Personnage non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($character->getUser()?->getId() !== $user->getId()) {
            return $this->json(['error' => 'Accès non autorisé'], JsonResponse::HTTP_FORBIDDEN);
        }

        return $this->json([
            'id' => $character->getId(),
            'name' => $character->getName(),
            'strength' => $character->getStrength(),
            'speed' => $character->getSpeed(),
            'durability' => $character->getDurability(),
            'power' => $character->getPower(),
            'combat' => $character->getCombat(),
            'user' => $character->getUser()?->getId(),
            'image' => $character->getImage()
        ]);
    }

    #[Route('/api/characters/{id}', name: 'api_character_options', methods: ['OPTIONS'])]
    public function optionsCharacter(): JsonResponse
    {
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/api/characters/{id}', name: 'api_character_update', methods: ['PATCH'])]
    #[IsGranted('ROLE_USER')]
    public function updateCharacter(int $id, Request $request, CharacterRepository $characterRepository, EntityManagerInterface $entityManager): JsonResponse
    {

        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $character = $characterRepository->find($id);
        if (!$character) {
            return $this->json(['error' => 'Personnage non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($character->getUser()?->getId() !== $user->getId()) {
            return $this->json(['error' => 'Accès non autorisé'], JsonResponse::HTTP_FORBIDDEN);
        }


        // Récupérer les données du formulaire
        $name = $request->request->get('name');
        $strength = $request->request->get('strength');
        $speed = $request->request->get('speed');
        $durability = $request->request->get('durability');
        $power = $request->request->get('power');
        $combat = $request->request->get('combat');

        // dd($name, $strength, $speed, $durability, $power, $combat);

        if (!$name || !$strength || !$speed || !$durability || !$power || !$combat) {
            return $this->json(['error' => 'Données invalides'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Gérer l'upload d'image
        $imageFile = $request->files->get('image');
        if ($imageFile) {
            $uploadDir = __DIR__ . '/../../../../next/public/uploads/characters/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = uniqid() . '.' . $imageFile->guessExtension();
            $imageFile->move($uploadDir, $fileName);
            
            // Supprimer l'ancienne image si elle existe
            if ($character->getImage()) {
                $oldImagePath = $uploadDir . basename($character->getImage());
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            
            $character->setImage($fileName);
        }

        $character->setName($name);
        $character->setStrength((float)$strength);
        $character->setSpeed((float)$speed);
        $character->setDurability((float)$durability);
        $character->setPower((float)$power);
        $character->setCombat((float)$combat);

        $entityManager->flush();

        return $this->json([
            'message' => 'Personnage mis à jour avec succès',
            'id' => $character->getId(),
            'image' => $character->getImage()
        ]);
    }

    #[Route('/api/characters/{id}', name: 'api_character_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function deleteCharacter(int $id, CharacterRepository $characterRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $character = $characterRepository->find($id);
        if (!$character) {
            return $this->json(['error' => 'Personnage non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($character->getUser()?->getId() !== $user->getId()) {
            return $this->json(['error' => 'Accès non autorisé'], JsonResponse::HTTP_FORBIDDEN);
        }

        $entityManager->remove($character);
        $entityManager->flush();

        return $this->json(['message' => 'Personnage supprimé avec succès'], JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/api/characters/add', name: 'app_character_add', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function createCharacter(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);
        if (!isset($data['name'], $data['strength'], $data['speed'], $data['durability'], $data['power'], $data['combat'])) {
            return $this->json(['error' => 'Données invalides'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $character = new Character();
        $character->setName($data['name']);
        $character->setStrength($data['strength']);
        $character->setSpeed($data['speed']);
        $character->setDurability($data['durability']);
        $character->setPower($data['power']);
        $character->setCombat($data['combat']);
        $character->setUser($user);

        $entityManager->persist($character);
        $entityManager->flush();

        return $this->json([
            'message' => 'Personnage créé avec succès',
            'id' => $character->getId()
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/characters/add', name: 'api_characters_add_options', methods: ['OPTIONS'])]
    public function optionsAdd(): JsonResponse
    {
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
