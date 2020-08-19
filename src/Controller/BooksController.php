<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

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
     * @Route("/books/create", name="app_books_create", methods={"GET", "POST"})
     */
    public function create(Request $request, EntityManagerInterface $em, Security $security)
    {
    	$book = new Book;
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $security->getUser();
            $book->setCreatedBy($user);
            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('books/create.html.twig',['form'=>$form->createView()]);
    }

    /**
     * @Route("/books/{id<[0-9]+>}", name="app_books_show", methods={"GET"})
     */
    public function show(Book $book): Response
    {
        return $this->render('books/show.html.twig', compact('book'));
    }
}
