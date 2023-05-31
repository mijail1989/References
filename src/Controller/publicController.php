<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/img')]
class publicController extends AbstractController
{
    #[Route('/{img}', name: 'img', methods: ['GET'])]
    public function index($img): Response
    {
        $filename = $this->getParameter('pictures_directory') . '/' . $img;
        if (file_exists($filename)) {
            return new BinaryFileResponse($filename);
        } 
        return new JsonResponse(['message' => 'Failed'], 403);
    }
}