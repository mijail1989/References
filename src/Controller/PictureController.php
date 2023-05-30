<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class PictureController extends AbstractController
{
    public function __construct(
        private Security $security,
    ) {
    }
    #[Route('/api/picture', name: 'app_picture', methods: ['POST'])]
    public function index(EntityManagerInterface $entityManager, ValidatorInterface $validator, SluggerInterface $slugger, UserRepository $userRepository, Request $request)
    {
        $img = $request->files->get("img");
        $constraint = new File([
            'maxSize' => '3072k',
            'mimeTypes' => ['image/*',],
            'mimeTypesMessage' => 'Please upload a valid Image',
        ]);
        $errors = $validator->validate($img, $constraint);
        if (count($errors)) {
            return new JsonResponse(['message' => 'Failed'], 403);
        }
        $originalFilename = pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $img->guessExtension();
        try {
            $img->move(
                $this->getParameter('pictures_directory'),
                $newFilename
            );
        } catch (FileException $e) {
            dd($e);
        }
        $owner = $this->security->getUser();
        $user = $userRepository->find($owner);
        $user->setImg($newFilename);
        $entityManager->persist($user);
        $entityManager->flush();
        return new JsonResponse(['message' => 'New Picture Added']);
    }
}
