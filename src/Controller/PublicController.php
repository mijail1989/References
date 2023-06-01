<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


#[Route('/public')]
class PublicController extends AbstractController
{
    // Asset Images ENDPOINT
    #[Route('/{img}', name: 'img', methods: ['GET'])]
    public function index($img): Response
    {
        $filename = $this->getParameter('pictures_directory') . '/' . $img;
        if (!file_exists($filename)) {
            return new JsonResponse(['message' => 'Failed'], 403);
        } 
        return new BinaryFileResponse($filename);
    }
    // Get User ENDPOINT
    #[Route('/user/{id}', name: 'user', methods: ['GET'])]
    public function userIndex($id,UserRepository $UserRepository): Response
    {
        $user = $UserRepository->find($id);
        if (!$user) {
            return new JsonResponse(['message' => 'Failed'], 403);
        } 
        return $this->json($user);
    }
}
