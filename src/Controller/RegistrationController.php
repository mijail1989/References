<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Skins;
use App\Event\EmailEvent;
use App\Repository\UserRepository;
use App\Validator\RegisterUserRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class RegistrationController extends AbstractController
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    #[Route('/registration', name: 'registration', methods: "post")]
    public function __invoke(
        RegisterUserRequest $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        VerifyEmailHelperInterface $verifyEmailHelper
    ): Response 
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
        $hashedPassword = $passwordHasher->hashPassword($user, $plaintextPassword);
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        $signatureComponents = $verifyEmailHelper->generateSignature(
            'verify_email',
            $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]
        );
        $emailEvent = new EmailEvent($user->getEmail(), $user->getName(), $signatureComponents->getSignedUrl());
        $this->eventDispatcher->dispatch($emailEvent, EmailEvent::REGISTRATION);

        return new JsonResponse(['message' => 'New User created, check your Email']);
    }

    //Verify Email new User ENDPOINT
    #[Route("/verify", name: "verify_email")]
    public function verifyUserEmail(Request $request, VerifyEmailHelperInterface $verifyEmailHelper, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
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
