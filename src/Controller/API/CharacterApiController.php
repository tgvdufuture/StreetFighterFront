<?php

namespace App\Controller\API;

use App\Entity\Character;
use App\Repository\CharacterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/character')]
class CharacterApiController extends AbstractController
{
    private CharacterRepository $characterRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(CharacterRepository $characterRepository, EntityManagerInterface $entityManager)
    {
        $this->characterRepository = $characterRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('', name: 'api_character_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $characters = $this->characterRepository->findAll();
        return $this->json($characters);
    }

    #[Route('', name: 'api_character_new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $character = new Character();
        
        // Assigner les propriétés du personnage
        $character->setName($data['name']);
        $character->setStrength($data['strength']);
        $character->setSpeed($data['speed']);
        $character->setDurability($data['durability']);
        $character->setPower($data['power']);
        $character->setCombat($data['combat']);

        $this->entityManager->persist($character);
        $this->entityManager->flush();

        return $this->json($character, JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_character_show', methods: ['GET'])]
    public function show(Character $character): JsonResponse
    {
        return $this->json($character);
    }

    #[Route('/{id}', name: 'api_character_edit', methods: ['PUT'])]
    public function edit(Request $request, Character $character): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Mettre à jour les propriétés du personnage
        $character->setName($data['name']);
        $character->setStrength($data['strength']);
        $character->setSpeed($data['speed']);
        $character->setDurability($data['durability']);
        $character->setPower($data['power']);
        $character->setCombat($data['combat']);

        $this->entityManager->flush();

        return $this->json($character);
    }

    #[Route('/{id}', name: 'api_character_delete', methods: ['DELETE'])]
    public function delete(Character $character): JsonResponse
    {
        $this->entityManager->remove($character);
        $this->entityManager->flush();

        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}