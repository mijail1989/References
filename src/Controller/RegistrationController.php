<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use App\Validator\RegisterUserRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/api/registration', name: 'api_registration', methods: "post")]
    public function index(RegisterUserRequest $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer, VerifyEmailHelperInterface $verifyEmailHelper): Response
    {

        $errors = $request->validate();
        if (count($errors)) {
            return new JsonResponse(['message' => 'Failed'], 403);
        }
        $body = $request->getRequest()->toArray();
        $user = new User();

        $user->setName($body["name"]);
        $user->setEmail($body["email"]);
        $user->setPhone($body["phone"]);
        $user->setRoles(["ROLE_USER"]);
        $user->setIsVerified(false);
        $plaintextPassword = $body["password"];
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $entityManager->persist($user);
        $entityManager->flush();
        $signatureComponents = $verifyEmailHelper->generateSignature(
            'verify_email',
            $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]
        );
        $email = (new Email())
            ->from('Registration@example.com')
            ->to($user->getEmail())
            ->subject('Registration Email')
            ->html("<p>Thank You {$user->getName()}! Click on the following link to complete your registration</p>
        <a href='{$signatureComponents->getSignedUrl()}'>click Here!</a>");
        $mailer->send($email);
        return new JsonResponse(['message' => 'New User created, check your Email']);
    }

    #[Route("/verify", name: "verify_email")]
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $idQuery = $request->query->get('id');
        $user = $userRepository->find($idQuery);
        if ($user) {
            $user->setIsVerified(true);
            return new JsonResponse(["message" => "Email Verified"]);
        }
        return new JsonResponse(["message" => "Error"], 403);
    }
}
