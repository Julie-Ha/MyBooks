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

    /**
     * @Route("/books/{id<[0-9]+>}/edit", name="app_books_edit", methods={"GET", "PUT"})
     */
    public function edit(Book $book, Request $request, EntityManagerInterface $em, Security $security): Response
    {
        if ($security->getUser() !== $book->getCreatedBy()) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(BookType::class, $book, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Livre mis à jour');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('books/edit.html.twig', ['form' => $form->createView(), 'book' => $book]);
    }

    /**
     * @Route("/books/{id<[0-9]+>}", name="app_books_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Book $book, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('book_deletion_'.$book->getId(), $request->request->get('csrf_token'))) {
            $em->remove($book);
            $em->flush();

            $this->addFlash('info', 'Livre supprimé');
        }
       

       return $this->redirectToRoute('app_home');
    }
}
