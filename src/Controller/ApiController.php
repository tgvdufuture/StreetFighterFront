<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CharacterRepository;
use App\Entity\Character;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ApiController extends AbstractController
{
    #[Route('/api/characters', name: 'api_characters', methods: ['GET'])]
    public function getAllCharacters(CharacterRepository $characterRepository): JsonResponse
    {
        $characters = $characterRepository->findAll();
        $data = [];

        foreach ($characters as $character) {
            $data[] = [
                'id' => $character->getId(),
                'name' => $character->getName(),
                'strength' => $character->getStrength(),
                'speed' => $character->getSpeed(),
                'durability' => $character->getDurability(),
                'power' => $character->getPower(),
                'combat' => $character->getCombat(),
                'user' => $character->getUser() ? $character->getUser()->getId() : null,
            ];
        }

        return $this->json($data);
    }

    #[Route('/api/characters/{id}', name: 'api_character_show', methods: ['GET'])]
    public function getCharacterById(int $id, CharacterRepository $characterRepository): JsonResponse
    {
        $character = $characterRepository->find($id);

        if (!$character) {
            return $this->json(['error' => 'Personnage non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $character->getId(),
            'name' => $character->getName(),
            'strength' => $character->getStrength(),
            'speed' => $character->getSpeed(),
            'durability' => $character->getDurability(),
            'power' => $character->getPower(),
            'combat' => $character->getCombat(),
            'user' => $character->getUser() ? $character->getUser()->getId() : null,
        ]);
    }

    #[Route('/api/characters/{id}', name: 'api_character_delete', methods: ['DELETE'])]
    public function deleteCharacter(int $id, CharacterRepository $characterRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $character = $characterRepository->find($id);

        if (!$character) {
            return $this->json(['error' => 'Personnage non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($character);
        $entityManager->flush();

        return $this->json(['message' => 'Personnage supprimé avec succès'], JsonResponse::HTTP_NO_CONTENT);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/api/characters/add', name: 'app_character_add', methods: ['POST'])]
    public function createCharacter(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'], $data['strength'], $data['speed'], $data['durability'], $data['power'], $data['combat'])) {
            return $this->json(['error' => 'Invalid data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Récupérer l'utilisateur à partir du token JWT
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non trouvé'], JsonResponse::HTTP_NOT_FOUND);
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

        return $this->json(['message' => 'Personnage créé avec succès', 'id' => $character->getId()], JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setEmail($data['email']);
        $user->setName($data['pseudo']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $user->setRoles(['ROLE_USER']);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['message' => 'Inscription réussie', 'id' => $user->getId()], JsonResponse::HTTP_CREATED);
    }
}

