<?php

namespace App\Controller;

use App\Entity\User;
use App\Validator\ResetPasswordRequest;
use App\Validator\ResetPasswordTokenRequest;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;


#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Display & process form to request a password reset.
     */
    #[Route('', name: 'app_forgot_password_request', methods: ["post"])]
    public function request(ResetPasswordRequest $request, MailerInterface $mailer, TranslatorInterface $translator): Response
    {
        $errors = $request->validate();
        if (count($errors)) {
            return new JsonResponse(['message' => 'Failed'], 403);
        }
        $body = $request->getRequest()->toArray();
        return $this->processSendingPasswordResetEmail(
            $body['email'],
            $mailer,
            $translator
        );
    }

    /**
     * Confirmation page after a user has requested a password reset.
     */
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }
        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     */
    #[Route('/reset/token', name: 'app_reset_password', methods: ["post"])]
    public function reset(ResetPasswordTokenRequest $request, UserPasswordHasherInterface $passwordHasher, string $token = null): Response
    {
        $errors = $request->validate();
        if (count($errors)) {
            return new JsonResponse(['message' => 'Failed'], 403);
        }
        $body = $request->getRequest()->toArray();
        $token = $body["token"];
        $password = $body["password"];
        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            return new JsonResponse(['message' => 'Failed'], 403);
        }

        $this->resetPasswordHelper->removeResetRequest($token);
        $encodedPassword = $passwordHasher->hashPassword(
            $user,
            $password
        );
        $user->setPassword($encodedPassword);
        $this->entityManager->flush();
        return new JsonResponse(['message' => 'Success'], 200);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): Response
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);
        if (!$user) {
            return new JsonResponse(['message' => "Ok"], 200);
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return new JsonResponse(['message' => "Ok"], 200);
        }
        $email = (new Email())
            ->from('Registration@example.com')
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->text('Here is the content of the email')
            ->html("<p>Token:{$resetToken->getToken()}</p>");
        $mailer->send($email);
        return new JsonResponse(['message' => "Ok"], 200);
    }
}
