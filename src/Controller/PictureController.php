<?php

namespace App\Controller;

use App\Entity\Picture;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\PictureFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

class PictureController extends AbstractController
{
    #[Route('/picture', name: 'app_picture')]
    public function index(Request $request, SluggerInterface $slugger,EntityManagerInterface $entityManager): Response
    {
        $picture = new Picture();

        $form = $this->createForm(PictureFormType::class, $picture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $urlFile = $form->get('url')->getData();

            $originalFilename = pathinfo($urlFile->getClientOriginalName(), PATHINFO_FILENAME);

            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $urlFile->guessExtension();
            try {
                $urlFile->move(
                    $this->getParameter('pictures_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                dd($e);
            }
            $picture->setUrl($newFilename);
            $entityManager->persist($picture);
            $entityManager->flush();
        }


        return $this->render('picture/index.html.twig', [
            'form' => $form,
        ]);
    }
}
