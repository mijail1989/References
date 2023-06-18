<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Contracts\Cache\CacheInterface;

#[AsController]
class PublicController extends AbstractController
{
    public function __construct(private CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    // Asset Images ENDPOINT
    // #[Route('/{img}', name: 'img', methods: ['GET'])]
    public function indexImg($img): Response
    {
        $cacheKey = 'pictures_' . $img;
        $response = $this->cache->get($cacheKey, function () use ($img) {
            $filename = $this->getParameter('pictures_directory') . '/' . $img;
            if (!file_exists($filename)) {
                return new JsonResponse(['message' => 'Failed'], 403);
            }
            return $filename;
        });
        if ($response === null) {
            return new JsonResponse(['message' => 'File not found'], 404);
        }
        return new BinaryFileResponse($response);
    }
    // Get User ENDPOINT
    // #[Route('/user/{id}', name: 'user', methods: ['GET'])]
    public function userIndex($id, UserRepository $UserRepository): Response
    {
        $cacheKey= 'user_' . $id;
        $response = $this->cache->get($cacheKey,function() use($id,$UserRepository){
            $user = $UserRepository->find($id);
            return $user;
        });
        if ($response === null) {
            return new JsonResponse(['message' => 'Failed'], 403);
        }
        return $this->json($response);
        // return new JsonResponse($response);
    }
}
