<?php
// src/Controller/HomeController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function index(): Response
    {
        if ($this->getUser()) {
            return $this->render('dashboard/index.html.twig');
        }
        
        return $this->render('public/home.html.twig');
    }
}