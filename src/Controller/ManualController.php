<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/manual', name: 'app_hello')]
class ManualController extends AbstractController
{

    #[Route('/list', name: 'manual_hello')]
    public function Hello()
    {
        return new Response("Hello every body!!!!");
    }
}
