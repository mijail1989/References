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

use Symfony\Component\HttpKernel\Attribute\AsController;


#[AsController]
class ReferenceController extends AbstractController
{
 
    public function __construct(private Security $security,)
    {
    }
    // Get Reference by ID and AuthUser ENDPOINT
    // #[Route('reference/{id}', name: 'reference_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, string $id): Response
    {
        $repository = $entityManager->getRepository(Reference::class);
        $reference = $repository->find($id);
        if (!$reference) {
            return new JsonResponse(["message" => "Error"], 403);
        }
        $user = $this->security->getUser();
        $owner = $reference->getUser();
        if ($user != $owner) {
            return new JsonResponse(['message' => 'Error'], 403);
        }
        return new JsonResponse($reference);
    }

    // Get all References created by AuthUser ENDPOINT
    // #[Route('/reference', name: 'reference', methods: ['GET'])]
    public function index(ReferenceRepository $referenceRepository): Response
    {
        $user = $this->security->getUser();
        return $this->json($referenceRepository->findByUser($user));
    }
    // Create New Reference ENDPOINT
    // #[Route('/reference/new', name: 'reference_new', methods: ['POST'])]
    public function new(CreateReferenceRequest $request, EntityManagerInterface $entityManager): Response
    {

        $errors = $request->validate();
        if (count($errors)) {
            return new JsonResponse(['message' => 'Failed'], 403);
        }
        $body = $request->getRequest()->toArray();
        $reference = new Reference();
        $user = $this->security->getUser();
        $reference->setReference($body, $user);
        $entityManager->persist($reference);
        $entityManager->flush();
        return new JsonResponse(['message' => 'New Reference created']);
    }

    // Edit Reference ENDPOINT
    // #[Route('/{id}/edit', name: 'reference_edit', methods: ['PUT'])]
    public function edit(CreateReferenceRequest $request, ReferenceRepository $referenceRepository, EntityManagerInterface $entityManager, $id): Response
    {
        $errors = $request->validate();
        if (count($errors)) {
            return new JsonResponse(['message' => 'Failed'], 403);
        }
        $user = $this->security->getUser();
        $body = $request->getRequest()->toArray();
        $reference = ($referenceRepository->findById($id));
        
        $reference->setReference($body, $user);
        $entityManager->persist($reference);
        $entityManager->flush();
        return new JsonResponse(['message' => 'New Reference updated']);
    }
    // Delete Reference ENDPOINT
    // #[Route('/{id}', name: 'reference_delete', methods: ['DELETE'])]
    public function delete(Reference $reference, ReferenceRepository $referenceRepository,$id): Response
    {
        $user = $this->security->getUser();
        $reference=$referenceRepository->findById($id);
        if($user==$reference->getUser()){
            $referenceRepository->remove($reference,true);
        }
        return  new JsonResponse(['message' => 'Reference Deleted']);
    }
}
