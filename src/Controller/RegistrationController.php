<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Skins;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use App\Validator\RegisterUserRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
        $repo = $entityManager->getRepository(Skins::class)->find($body["skin_id"]);
        $user->setSkin($repo);
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
    public function verifyUserEmail(Request $request, VerifyEmailHelperInterface $verifyEmailHelper, UserRepository $userRepository,EntityManagerInterface $entityManager): Response
    {
        $idQuery = $request->query->get('id');
        $user = $userRepository->find($idQuery);
        if (!$user) {
            return new JsonResponse(["message" => "Error"], 403);
        }
        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail(),
            );
        } catch (VerifyEmailExceptionInterface $e) {
            return new JsonResponse(['error', $e->getReason()], 403);
        }
        $user->setIsVerified(true);
        $entityManager->flush();
        return new JsonResponse(["message" => "Email Verified"]);
    }
}
