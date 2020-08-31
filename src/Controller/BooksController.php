<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\UserBook;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Repository\UserBookRepository;

class BooksController extends AbstractController
{
    /**
     * @Route("/api/getBooks/{offset?}")
     */
    public function getBooks(BookRepository $bookRepository, $offset) {
        return $this->json(['books' => $bookRepository->getBooks($offset),
                            'total' => $bookRepository->getCountBooks()]);
    }

    /**
     * @Route("/", name="app_home")
     */
    public function index(BookRepository $bookRepository)
    {
        $title = 'Derniers livres';
        $books = $bookRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('books/index.html.twig', compact('title', 'books'));
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
    public function show(Book $book, Security $security, UserBookRepository $userBookRepository)
    {   
        $isInLibrary = false;
        $isRead = false;
        $readingDate = null;

        if ($security->getUser()) {
            $userBook = $userBookRepository->findUserBook($security->getUser(), $book);

            if ($userBook) {
                $userBook = $userBook[0];
                $isInLibrary = true;
                $isRead = $userBook->getIsRead();
                $readingDate = $userBook->getReadAt();
                
                if($readingDate) {
                    $readingDate = $readingDate->format('d/m/Y');
                }
            }
        }

        return $this->render('books/show.html.twig', compact('book', 'isInLibrary', 'isRead', 'readingDate'));
    }

    /**
     * @Route("/books/{id<[0-9]+>}/edit", name="app_books_edit", methods={"GET", "PUT"})
     */
    public function edit(Book $book, Request $request, EntityManagerInterface $em, Security $security)
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
    public function delete(Request $request, Book $book, EntityManagerInterface $em, Security $security)
    {
        if ($security->getUser() !== $book->getCreatedBy()) {
            return $this->redirectToRoute('app_home');
        }

        if ($this->isCsrfTokenValid('book_deletion_'.$book->getId(), $request->request->get('csrf_token'))) {
            $em->remove($book);
            $em->flush();

            $this->addFlash('info', 'Livre supprimé');
        }
       

       return $this->redirectToRoute('app_home');
    }

    /**
     * @Route("/books/search", name="app_books_search", methods={"GET", "POST"})
     */
    public function search(Request $request, BookRepository $bookRepository, EntityManagerInterface $em)
    {
        $data = $request->request->all();

        $books = $bookRepository->findBooksBySearch($data['search']);

        $title = 'Résultats de la recherche "'.$data['search'].'"';
        return $this->render('books/index.html.twig', compact('title', 'books'));
    }
}
