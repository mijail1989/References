<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    #[Route('/movies/{name}', name: 'app_movies', methods:["GET","HEAD"],defaults:["name"=>NULL])]
    public function index($name): JsonResponse
    {
        if(!$name){
           return $this->json([
                'message' => "Non hai assegnato alcun nome",
                'path' => 'src/Controller/MoviesController.php',
            ]);
        }
      
        return $this->json([
            'message' => "Welcome $name",
            'path' => 'src/Controller/MoviesController.php',
        ]);
    }
}
