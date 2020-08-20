<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class LibraryController extends AbstractController
{
    /**
     * @Route("/library", name="app_library")
     */
    public function index(Security $security)
    {
    	$title = "Mes livres";
        $books = $security->getUser()->getBooks();
        return $this->render('books/index.html.twig', compact('title', 'books'));
    }
}
