<?php

namespace App\Validator;

use App\Validator\BaseRequest;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateReferenceRequest extends BaseRequest
{
    #[Type('string')]
    #[NotBlank()]
    #[Length(min:5,max: 150)]
    protected $name;
    #[Type('string')]
    #[NotBlank()]
    #[Length(min:5,max: 150)]
    protected $url;
    #[Type('string')]
    #[NotBlank()]
    #[Length(2)]
    protected $lang;
    #[Type('string')]
    #[NotBlank()]
    #[Length(min:8,max: 500)]
    protected $description;
    #[Type('string')]
    #[NotBlank()]
    protected $img;
}