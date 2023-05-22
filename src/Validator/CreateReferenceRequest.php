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
    #[Length(min:5,max: 25)]
    protected $name;
    #[Type('string')]
    #[NotBlank()]
    #[Length(min:5,max: 25)]
    protected $url;
    #[Type('string')]
    #[NotBlank()]
    #[Length(min:5,max: 25)]
    protected $lang;
    #[Type('string')]
    #[NotBlank()]
    #[Length(min:15,max: 150)]
    protected $description;
    #[Type('string')]
    #[NotBlank()]
    #[Length(min:5,max: 150)]
    protected $img;
}