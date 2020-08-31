<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\UserBook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BookRepository;
use App\Repository\UserBookRepository;

class LibraryController extends AbstractController
{
    /**
     * @Route("/library/books", name="app_library_books")
     */
    public function index(Security $security, BookRepository $bookRepository, UserBookRepository $userBookRepository)
    {
        $title = "Mes livres";
        $userBooks = $userBookRepository->findByUser($security->getUser());

        $books = [];
        foreach($userBooks as $userBook) {
            $books[] = $bookRepository->findById($userBook->getBook()->getId())[0];
        }

        return $this->render('books/index.html.twig', compact('title', 'books'));
    }

    /**
     * @Route("/library/books/{id<[0-9]+>}/add", name="app_library_add", methods={"PUT"})
     */
    public function add(Book $book, Request $request, EntityManagerInterface $em, Security $security)
    {
        if (!$security->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isCsrfTokenValid('book_add_'.$book->getId(), $request->request->get('csrf_token'))) {
            $user_book = new UserBook();
            $user_book->setUser($security->getUser());
            $user_book->setBook($book);
            $user_book->setIsRead(false);
            $em->persist($user_book);
            $em->flush();

            $this->addFlash('info', 'Livre ajouté');
        }

        return $this->redirect($this->generateUrl('app_books_show', ['id' => $book->getId()]));
    }

    /**
     * @Route("/library/books/{id<[0-9]+>}/remove", name="app_library_remove", methods={"DELETE"})
     */
    public function remove(Book $book, Request $request, EntityManagerInterface $em, Security $security, UserBookRepository $userBookRepository)
    {
        if (!$security->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isCsrfTokenValid('book_remove_'.$book->getId(), $request->request->get('csrf_token'))) {
            $userBook = $userBookRepository->findUserBook($security->getUser(), $book)[0];
            $em->remove($userBook);
            $em->flush();

            $this->addFlash('info', 'Livre retiré');
        }

        return $this->redirect($this->generateUrl('app_books_show', ['id' => $book->getId()]));
    }

    /**
     * @Route("/library/books/{id<[0-9]+>}/read", name="app_library_read", methods={"PUT"})
     */
    public function read(Book $book, Request $request, EntityManagerInterface $em, Security $security, UserBookRepository $userBookRepository)
    {
        if (!$security->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isCsrfTokenValid('book_read_'.$book->getId(), $request->request->get('csrf_token'))) {
            $userBook = $userBookRepository->findUserBook($security->getUser(), $book)[0];
            $userBook->setIsRead(true);
            $em->flush();

            $this->addFlash('info', 'Livre marqué comme lu');
        }

        return $this->redirect($this->generateUrl('app_books_show', ['id' => $book->getId()]));
    }

    /**
     * @Route("/library/books/{id<[0-9]+>}/notread", name="app_library_notread", methods={"DELETE"})
     */
    public function notRead(Book $book, Request $request, EntityManagerInterface $em, Security $security, UserBookRepository $userBookRepository)
    {
        if (!$security->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isCsrfTokenValid('book_notread_'.$book->getId(), $request->request->get('csrf_token'))) {
            $userBook = $userBookRepository->findUserBook($security->getUser(), $book)[0];
            $userBook->setIsRead(false);
            $userBook->setReadAt(null);
            $em->flush();

            $this->addFlash('info', 'Livre marqué comme non lu');
        }

        return $this->redirect($this->generateUrl('app_books_show', ['id' => $book->getId()]));
    }

    /**
     * @Route("/library/books/{id<[0-9]+>}/addDate", name="app_library_readingdate", methods={"PUT"})
     */
    public function addReadingDate(Book $book, Request $request, EntityManagerInterface $em, Security $security, UserBookRepository $userBookRepository)
    {
        if (!$security->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isCsrfTokenValid('book_readingdate_'.$book->getId(), $request->request->get('csrf_token'))) {
            $date = new \DateTime($request->request->get('reading_date'));
            $userBook = $userBookRepository->findUserBook($security->getUser(), $book)[0];
            $userBook->setReadAt($date);
            $em->flush();

            $this->addFlash('info', 'Livre marqué comme non lu');
        }

        return $this->redirect($this->generateUrl('app_books_show', ['id' => $book->getId()]));
    }

}
