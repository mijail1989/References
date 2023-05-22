<?php

namespace App\Controller;

use App\Entity\Reference;
use App\Form\ReferenceType;
use App\Repository\ReferenceRepository;
use App\Validator\CreateReferenceRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/api/reference')]
class ReferenceController extends AbstractController
{
    public function __construct(
        private Security $security,
    ) {
    }

    #[Route('/', name: 'app_reference_index', methods: ['GET'])]

    public function index(ReferenceRepository $referenceRepository): Response
    {
        $user = $this->security->getUser();
        return $this->json($referenceRepository->findByUser($user));
    }

    #[Route('/new', name: 'app_reference_new', methods: ['GET', 'POST'])]
    public function new(CreateReferenceRequest $request, EntityManagerInterface $entityManager): Response
    {

        $errors = $request->validate();
        if (count($errors)) {
            return new JsonResponse(['message' => 'Failed'], 403);
        }
        $body = $request->getRequest()->toArray();
        $reference = new Reference();
        $reference->setName($body["name"]);
        $reference->setUrl($body["url"]);
        $reference->setLang($body["lang"]);
        $reference->setDescription($body["description"]);
        $reference->setImg($body["img"]);
        $reference->setUser($this->security->getUser());
        $entityManager->persist($reference);
        $entityManager->flush();
        return new JsonResponse(['message' => 'New Reference created']);
    }

    #[Route('/{id}', name: 'app_reference_show', methods: ['GET'])]
    public function show(ReferenceRepository $referenceRepository,$id): Response
    {
        $user=$this->security->getUser();
        $referenceId=($referenceRepository->findByUserAndId($user,$id));
        $status=$referenceId? 200: 403;
        return new JsonResponse($referenceId,$status);
    }

    #[Route('/{id}/edit', name: 'app_reference_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reference $reference, ReferenceRepository $referenceRepository): Response
    {
        $form = $this->createForm(ReferenceType::class, $reference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $referenceRepository->save($reference, true);

            return $this->redirectToRoute('app_reference_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reference/edit.html.twig', [
            'reference' => $reference,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reference_delete', methods: ['POST'])]
    public function delete(Request $request, Reference $reference, ReferenceRepository $referenceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'. $reference->getId(), $request->request->get('_token'))) {
            $referenceRepository->remove($reference, true);
        }

        return $this->redirectToRoute('app_reference_index', [], Response::HTTP_SEE_OTHER);
    }
}
