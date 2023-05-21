<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// use App\Repository\UserRepository

class RegistrationController extends AbstractController
{
    #[Route('/api/registration', name: 'api_registration', methods: "post")]
    public function index(Request $request,EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response
    {
        // dd($request->getContent());
        $body= json_decode($request->getContent(), true);
        
        $user = new User();
        $user->setName($body["name"]);
        $user->setEmail($body["email"]);
        $user->setPhone($body["phone"]);
        $plaintextPassword = $body["password"];
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $entityManager->persist($user);
        $entityManager->flush();

        return new Response('Saved new product with id '.$user->getId());
    }
}
