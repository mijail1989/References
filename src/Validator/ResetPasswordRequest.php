<?php

namespace App\Validator;

use App\Validator\BaseRequest;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordRequest extends BaseRequest
{
    #[Type('string')]
    #[NotBlank()]
    #[Length(min:5,max: 25)]
    protected $email;
}