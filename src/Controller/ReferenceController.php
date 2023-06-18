<?php

namespace App\Controller;

use App\Entity\Reference;
use App\Repository\ReferenceRepository;
use App\Validator\CreateReferenceRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;


#[AsController]
class ReferenceController extends AbstractController
{

    public function __construct(private Security $security, CacheInterface $cache, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->cache = $cache;
        $this->entityManager = $entityManager;
    }
    // Get Reference by ID and AuthUser ENDPOINT
    // #[Route('reference/{id}', name: 'reference_show', methods: ['GET'])]
    public function show(string $id): Response
    {
        $cacheKey = 'reference_' . $id;

        // Try to get the response from cache
        $response = $this->cache->get($cacheKey, function () use ($id) {
            $repository = $this->entityManager->getRepository(Reference::class);
            $reference = $repository->find($id);
            if (!$reference) {
                return new JsonResponse(["message" => "Error"], 403);
            }
            $user = $this->security->getUser();
            $owner = $reference->getUser();
            if ($user != $owner) {
                return new JsonResponse(['message' => 'Error'], 403);
            }
            return $reference;
        });
        return new JsonResponse($response);
    }

    // Get all References created by AuthUser ENDPOINT
    // #[Route('/reference', name: 'reference', methods: ['GET'])]
    public function index(ReferenceRepository $referenceRepository): Response
    {
        $cacheKey = 'reference';
        $response =$this->cache->get($cacheKey,function() use($referenceRepository){
            $user = $this->security->getUser();
            $reference = $referenceRepository->findByUser($user);
            return $reference;
        });
        return new JsonResponse($response);
    }
    // Create New Reference ENDPOINT
    // #[Route('/reference/new', name: 'reference_new', methods: ['POST'])]
    public function new(CreateReferenceRequest $request): Response
    {

        $errors = $request->validate();
        if (count($errors)) {
            return new JsonResponse(['message' => 'Failed'], 403);
        }
        $body = $request->getRequest()->toArray();
        $reference = new Reference();
        $user = $this->security->getUser();
        $reference->setReference($body, $user);
        $this->entityManager->persist($reference);
        $this->entityManager->flush();

        $cacheKey = 'reference';
        if ($this->cache->hasItem($cacheKey)) {
            $this->cache->delete($cacheKey);
        }
        return new JsonResponse(['message' => 'New Reference created']);
    }

    // Edit Reference ENDPOINT
    // #[Route('/{id}/edit', name: 'reference_edit', methods: ['PUT'])]
    public function edit(CreateReferenceRequest $request, ReferenceRepository $referenceRepository, $id): Response
    {
        $errors = $request->validate();
        if (count($errors)) {
            return new JsonResponse(['message' => 'Failed'], 403);
        }
        $user = $this->security->getUser();
        $body = $request->getRequest()->toArray();
        $reference = ($referenceRepository->findById($id));

        $reference->setReference($body, $user);
        $this->entityManager->persist($reference);
        $this->entityManager->flush();

        // Aggiorna la cache con il nuovo valore
        $cacheKey = 'reference_' . $id;
        $item = $this->cache->getItem($cacheKey);
        $item->expiresAfter(3600); // Imposta la durata della cache a 1 ora
        $item->set($reference); // Reimposta il valore nella cache
        $this->cache->save($item);
        return new JsonResponse(['message' => 'New Reference updated']);
    }
    // Delete Reference ENDPOINT
    // #[Route('/{id}', name: 'reference_delete', methods: ['DELETE'])]
    public function delete(Reference $reference, ReferenceRepository $referenceRepository, $id): Response
    {
        $user = $this->security->getUser();
        $reference = $referenceRepository->findById($id);
        if ($user == $reference->getUser()) {
            $referenceRepository->remove($reference, true);
        }
        return  new JsonResponse(['message' => 'Reference Deleted']);
    }
}
