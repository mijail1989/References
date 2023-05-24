<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Skins;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/api/skins')]

class SkinsController extends AbstractController
{
    public function __construct(
        private Security $security,
    ) {
    }

    #[Route('/', name: 'app_skins_index', methods: ['GET'])]

    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->security->getUser();
        if ( $user){

            return $this->json($skins = $entityManager->getRepository(Skins::class)->findAll());
        }
    }
}
