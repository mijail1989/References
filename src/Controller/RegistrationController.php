<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Validator\RegisterUserRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/api/registration', name: 'api_registration', methods: "post")]
    public function index(RegisterUserRequest $request,EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher,MailerInterface $mailer): Response
    {

        $errors = $request->validate();
        if(count($errors)){
            return new JsonResponse(['message' => 'Failed'], 403);
        }
        $body= $request->getRequest()->toArray();
        $user = new User();

        $user->setName($body["name"]);
        $user->setEmail($body["email"]);
        $user->setPhone($body["phone"]);
        $user->setRoles(["ROLE_USER"]);
        $plaintextPassword = $body["password"];
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $entityManager->persist($user);
        $entityManager->flush();

        $email = (new Email())
        ->from('Registration@example.com')
        ->to($user->getEmail())
        ->subject('Registration Email')
        ->text("Thank You {$user->getName()}! Your registration has been setted up");
        $mailer->send($email);
        return new JsonResponse(['message' => 'New User created']);
    }
}
