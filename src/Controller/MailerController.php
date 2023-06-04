<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class MailerController extends AbstractController
{
    #[Route('/email')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        $user=[
            "name" =>"Krokkin Colli",
            "email" =>"Caccioletta@email.com"
        ];


        $email = (new TemplatedEmail())
            ->from('hello@example.com')
            ->to('p.rellifederico@gmail.com')
            // ->priority(Email::PRIORITY_HIGH)
            ->subject('Fai cagare')
            ->htmlTemplate('emails/singup.html.twig')
            ->context([
                "username"=> $user
            ]);
        $mailer->send($email);

        return new JsonResponse(['message' => 'New User Perrito']);
    }
}
