<?php

namespace App\Controller;

use App\Entity\Reference;
use App\Form\ReferenceType;
use App\Repository\ReferenceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/reference')]
class ReferenceController extends AbstractController
{
    #[Route('/', name: 'app_reference_index', methods: ['GET'])]
    public function index(ReferenceRepository $referenceRepository): Response
    {

        return $this->json($referenceRepository->findAll());
    }

    #[Route('/new', name: 'app_reference_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ReferenceRepository $referenceRepository): Response
    {
        $reference = new Reference();
        $form = $this->createForm(ReferenceType::class, $reference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $referenceRepository->save($reference, true);

            return $this->redirectToRoute('app_reference_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reference/new.html.twig', [
            'reference' => $reference,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reference_show', methods: ['GET'])]
    public function show(Reference $reference): Response
    {
        return $this->render('reference/show.html.twig', [
            'reference' => $reference,
        ]);
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
        if ($this->isCsrfTokenValid('delete'.$reference->getId(), $request->request->get('_token'))) {
            $referenceRepository->remove($reference, true);
        }

        return $this->redirectToRoute('app_reference_index', [], Response::HTTP_SEE_OTHER);
    }
}
