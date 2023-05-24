<?php

namespace App\Validator;

use App\Validator\BaseRequest;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\In;

class RegisterUserRequest extends BaseRequest
{
    #[Type('string')]
    #[NotBlank()]
    #[Length(min:3,max: 25)]
    protected $name;
    #[Type('string')]
    #[NotBlank()]
    #[Length(min:5,max: 25)]
    protected $email;
    #[Type('string')]
    #[NotBlank()]
    #[Length(min:5,max: 20)]
    protected $phone;
    #[Type('string')]
    #[NotBlank()]
    #[Length(min:5,max: 150)]
    protected $password;
    #[Type('int')]
    protected $skin_id;
}