<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;

final class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/insertBook', name: 'book_insertBook')]
    public function insertBook(EntityManagerInterface $mr, Request $request): Response
    {
        $book = new Book();

        $book->setPublished(true);
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $author = $book->getAuthor();
            if ($author) {
                $author->setNbBooks($author->getNbBooks() + 1);
                $mr->persist($author);
            }
            $mr->persist($book);
            $mr->flush();
            return $this->redirectToRoute("book_getBooks");
        }
        return $this->render('book/form.html.twig', [
            'bookForm' => $form,
        ]);
    }

    #[Route('/getBooks', name: 'book_getBooks')]
    public function getBooks(BookRepository $bookRepo): Response
    {
        //$books = $bookRepo->findBy(['published' => true]);
        $books = $bookRepo->booksListByAuthors();
        return $this->render('book/list.html.twig', [
            "books" => $books,
        ]);
    }

    #[Route('/updateBook/{id}', name: 'book_updateBook')]
    public function updateBook(EntityManagerInterface $mr, Request $request, $id): Response
    {
        $book = new Book();
        $book = $mr->getRepository(Book::class)->find($id);
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $mr->persist($book);
            $mr->flush();
            return $this->redirectToRoute("book_getBooks");
        }

        return $this->render('book/form.html.twig', [
            'bookForm' => $form,
        ]);
    }

    #[Route('/deleteBook/{id}', name: 'book_deleteBook')]
    public function deleteBook(EntityManagerInterface $mr, Request $request, $id): Response
    {
        $book = $mr->getRepository(Book::class)->find($id);
        $mr->remove($book);
        $mr->flush();
        return $this->redirectToRoute("book_getBooks");
    }

    #[Route('/detailsBook/{id}', name: 'book_detailsBook')]
    public function detailsBook(BookRepository $bookRepo, $id): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $bookRepo->find($id),
        ]);
    }

    #[Route('/getBookByRef', name: 'book_getBookByRef')]
    public function getBookByRef(BookRepository $bookRepo, Request $request): Response
    {
        $ref = $request->query->get('ref');
        $booksByRef = $bookRepo->searchBookByRef($ref);
        return $this->render('book/listBookByRef.html.twig', [
            "list" => $booksByRef,
            "ref" => $ref,
        ]);
    }

    #[Route('/booksBefore2023', name: 'book_listBooksBefore2023')]
    public function listBooksBefore2023(BookRepository $bookRepo): Response
    {
        $books = $bookRepo->booksBefore2023();
        return $this->render('book/listBooksBefore2023.html.twig', [
            "list" => $books,
        ]);
    }

    #[Route('/updateCategory', name: 'book_updateCategory')]
    public function updateCategory(BookRepository $bookRepo): Response
    {
        $update = $bookRepo->updateCategoryScience();
        $books = $bookRepo->findAll();
        return $this->render('book/listBooksCategoryUpdated.html.twig', [
            "list" => $books,
        ]);
    }
}