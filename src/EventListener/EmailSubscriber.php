<?php
namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\EmailEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class EmailSubscriber implements EventSubscriberInterface
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EmailEvent::REGISTRATION => 'sendEmail',
        ];
    }

    public function sendEmail(EmailEvent $event): void
    {
        $email = (new TemplatedEmail())
            ->from('Registration@example.com')
            ->to($event->getEmail())
            ->subject('Registration Email')
            ->htmlTemplate('emails/registration.html.twig')
            ->context([
                'username' => $event->getUsername(),
                'signature' => $event->getSignature(),
            ]);

        $this->mailer->send($email);
    }
}