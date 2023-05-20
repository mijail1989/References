<?php

namespace App\Controller;

use App\Entity\Reference;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SwagController extends AbstractController
{
    #[Route('/swag', name: 'swag',methods: ['GET'])]
   public function index(Reference $reference): Response
    {
        dd($reference);
        $all=$reference;
        
         return new JsonResponse($all);
        // $number = random_int(0, 100);

        // return $this->render();
    }
}
