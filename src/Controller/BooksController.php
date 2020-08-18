<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BooksController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index()
    {
        return $this->render('books/index.html.twig', [
            'controller_name' => 'BooksController',
        ]);
    }
}
