<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BooksController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(BookRepository $bookRepository)
    {
        $books = $bookRepository->findAll();
        return $this->render('books/index.html.twig', compact('books'));
    }

    /**
     * @Route("/books/create", name="app_books_create")
     */
    public function create(Request $request, EntityManagerInterface $em)
    {
    	$book = new Book;
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('books/create.html.twig',['form'=>$form->createView()]);
    }
}
