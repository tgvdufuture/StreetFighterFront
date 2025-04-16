<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Character;

class ArenaController extends AbstractController
{
    #[Route('/arenes', name: 'app_arena')]
    public function index()
    {
        // Récupérer tous les personnages
        $characters = $this->getDoctrine()
            ->getRepository(Character::class)
            ->findAll();

        return $this->render('arena/index.html.twig', [
            'characters' => $characters,
        ]);
    }
}
