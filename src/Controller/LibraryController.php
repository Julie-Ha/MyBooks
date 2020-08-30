<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Repository\BookRepository;
use App\Repository\UserBookRepository;

class LibraryController extends AbstractController
{
    /**
     * @Route("/library", name="app_library")
     */
    public function index(Security $security, BookRepository $bookRepository, UserBookRepository $userBookRepository)
    {
    	$title = "Mes livres";
        $books = $bookRepository->findBooksByUser($security->getUser(), $userBookRepository);
        dd($books);
        return $this->render('books/index.html.twig', compact('title', 'books'));
    }
}
