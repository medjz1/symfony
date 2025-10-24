<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Author;
use App\Form\AuthorType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/showAuthor/{name}', name: 'app_showAuthor')]
    public function showAuthor($name): Response
    {
        return $this->render('author/show.html.twig', [
            'author_name' => $name,
        ]);
    }

    #[Route('/listAuthor', name: 'app_listAuthors')]
    public function listAuthor(): Response
    {
        $authors = array(
            array('id' => 1, 'picture' => '/images/VictorHugo.jpg', 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com ', 'nb_books' => 100),
            array('id' => 2, 'picture' => '/images/william.jpg', 'username' => ' William Shakespeare', 'email' =>  ' william.shakespeare@gmail.com', 'nb_books' => 200),
            array('id' => 3, 'picture' => '/images/TahaHessin.jpg', 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300),
        );
        return $this->render('author/list.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/detailsAuthor/{id}', name: 'app_detailsAuthors')]
    public function detailsAuthor($id)
    {
        $authors = array(
            array('id' => 1, 'picture' => '/images/VictorHugo.jpg', 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com ', 'nb_books' => 100),
            array('id' => 2, 'picture' => '/images/william.jpg', 'username' => ' William Shakespeare', 'email' =>  ' william.shakespeare@gmail.com', 'nb_books' => 200),
            array('id' => 3, 'picture' => '/images/TahaHessin.jpg', 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300),
        );

        $author = null;
        foreach ($authors as $a) {
            if ($a['id'] == $id) {
                $author = $a;
            }
        }
        return $this->render('author/showAuthor.html.twig', [
            'author' => $author,
        ]);
    }

    //Find authors
    #[Route('/authors', name: 'author_getAuthors')]
    public function getAuthors(AuthorRepository $authRepo): Response
    {
        //$authors = $authRepo->findAll();
        $authors = $authRepo->findByEmail();
        return $this->render('author/list.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/all', name: 'author_getAll')]
    public function getAll(ManagerRegistry $mr)
    {
        $manager = $mr->getManager();
        $authors = $manager->getRepository(Author::class)->findAll();
        return $this->render('author/list.html.twig', [
            'authors' => $authors,
        ]);
    }

    //add author static
    #[Route('/addAuthor', name: 'author_addAuthor')]
    public function addAuthor(EntityManagerInterface $mr): Response
    {
        $author = new Author();
        $author->setUsername("Abulkasem");
        $author->setEmail("abulkasem@gmail.com");
        $author->setNbBooks(150);
        if ($author) {
            $mr->persist($author);
            $mr->flush();
        }
        return $this->redirectToRoute("author_getAuthors");
    }

    //CRUD with form
    #[Route('/insert', name: 'author_insertAuthor')]
    public function insertAuthor(EntityManagerInterface $mr, Request $request)
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $mr->persist($author);
            $mr->flush();
            return $this->redirectToRoute("author_getAuthors");
        }
        return $this->render('author/form.html.twig',[
            'authorForm' => $form,
        ]);
    }

    #[Route('/update/{id}', name: 'author_updateAuthor')]
    public function updateAuthor(EntityManagerInterface $mr, Request $request,$id): Response
    {
        $author = new Author();
        $author = $mr->getRepository(Author::class)->find($id);
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $mr->persist($author);
            $mr->flush();
            return $this->redirectToRoute("author_getAuthors");
        }

        return $this->render('author/form.html.twig', [
            'authorForm' => $form,
        ]);
    }

    #[Route('/delete/{id}', name:"author_deleteAuthor")]
    public function deleteAuthor(EntityManagerInterface $mr, Request $request, $id): Response
    {
        $author = $mr->getRepository(Author::class)->find($id);
        $mr->remove($author);
        $mr->flush();
        return $this->redirectToRoute("author_getAuthors");
    }

    #[Route('/details/{id}', name: 'author_getAuthor')]
    public function getAuthor(AuthorRepository $authRepo, $id):Response
    {
        return $this->render('author/showAuthor.html.twig',[
            'auth'=>$authRepo->find($id),
        ]);
    }
}