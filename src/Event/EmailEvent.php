<?php
namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class EmailEvent extends Event
{
    public const REGISTRATION = 'sendEmail';
    private $email;
    private $username;
    private $signature;

    public function __construct(string $email, string $username, string $signature)
    {
        $this->email = $email;
        $this->username = $username;
        $this->signature = $signature;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getSignature(): string
    {
        return $this->signature;
    }
}