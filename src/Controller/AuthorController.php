<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Container5iqVUgI\getAuthorRepositoryService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthorController extends AbstractController
{
    private $authors;
    public function __construct()
    {
        $this->authors = array(
            array('id' => 1, 'picture' => '/images/Victor-Hugo.jpg', 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com ', 'nb_books' => 100),
            array('id' => 2, 'picture' => '/images/william-shakespeare.jpeg', 'username' => ' William Shakespeare', 'email' =>  ' william.shakespeare@gmail.com', 'nb_books' => 200),
            array('id' => 3, 'picture' => '/images/Taha_Hussein.jpg', 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300),
        );
    }

    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }


    #[Route('/list', name: 'author_list')]
    public function authorList(): Response
    {
        return $this->render('author/list.html.twig', [
            'authors' => $this->authors,
        ]);
    }

    //serach by id
    public function serchById($id): null|array
    {
        foreach ($this->authors as $author) {
            if ($author['id'] == $id) {
                return $author;
            }
        }
        return null;
    }

    #[Route('/authorDetails/{id}', name: 'author_authorDetails')]
    public function authorDetails($id): Response
    {
        $auth = $this->serchById($id);

        return $this->render('author/showAuthor.html.twig', [
            'auth' => $auth
        ]);
    }



    //  CRUD WITH DATABASE


    //with repository
    #[Route('/authors', name: 'author_getAuthors')]
    public function getAuthors(AuthorRepository $authRepo, Request $req): Response
    {
        $name = $req->query->get('author');
        if ($name) {
            $authors = $authRepo->findByName($name);
        } else {
            $authors = $authRepo->findAll();
        }

        return $this->render('author/list.html.twig', [
            'authors' => $authors,
            'name' => $name
        ]);
    }
    //with manager regitry// ENtityManagerInterface
    #[Route('/all', name: 'author_getAll')]
    public function getAll(EntityManagerInterface $me): Response
    {
        $authors = $me->getRepository(Author::class)->findAll();
        return $this->render('author/list.html.twig', [
            'authors' => $authors,
        ]);
    }

    //with manager regitry
    #[Route('/alll', name: 'author_getAlll')]
    public function getAlll(ManagerRegistry $mr): Response
    {
        $manager = $mr->getManager();
        $authors = $manager->getRepository(Author::class)->findAll();
        return $this->render('author/list.html.twig', [
            'authors' => $authors,
        ]);
    }


    //add static
    #[Route('/add', name: 'author_addAuthor')]
    public function addAuthor(EntityManagerInterface $mr): Response
    {
        // $manager = $mr->getManager()->getRepository(Author::class)->findAll();;
        $author = new Author();
        $author->setUsername("abouelkassem");
        $author->setEmail("abouelkassem@gmail.com");
        $author->setNbBook(50);
        // if ($author) {
        $mr->persist($author);
        $mr->flush();
        // return new Response('added with succes');
        // }

        return $this->redirectToRoute('author_getAuthors');
    }


    //add with form

    #[Route('/insert', name: 'author_insertAuthor')]
    public function insertAuthor(EntityManagerInterface $mr, Request $request): Response
    {   // objet ou mettre les données
        $author = new Author();
        $book = new Book();
        $book->setTitle('les amis');
        $book->setPublished(true);
        $author->addBook($book);
        // créer un formulaire et l'associer à l'objet $author
        $form = $this->createForm(AuthorType::class, $author);

        //remplir l'objet $author à partir du request
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mr->persist($author); // générer le code sql sans exécution dans doctrine
            $mr->flush(); // exécuter la requette dans la BD

            return $this->redirectToRoute('author_getAuthors');
        }

        return $this->render('author/form.html.twig', [
            'authorForm' => $form,
        ]);
    }


    // get author By id: details

    #[Route('/details/{id}', name: 'author_detailAuthor')]
    public function getAuthor(AuthorRepository  $authRepo, $id): Response
    {
        return $this->render('author/showAuthor.html.twig', [
            'auth' => $authRepo->find($id),
        ]);
    }

    //updatewith form

    #[Route('/update/{id}', name: 'author_updateAuthor')]
    public function updateAuthor(EntityManagerInterface $mr, Request $request, $id): Response
    {
        $author = new Author();

        // get author By id to be updated
        $author = $mr->getRepository(Author::class)->find($id);
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $mr->persist($author);
            $mr->flush();

            return $this->redirectToRoute('author_getAuthors');
        }

        return $this->render('author/form.html.twig', [
            'authorForm' => $form,
        ]);
    }

    //delete author
    #[Route('/delete/{id}', name: 'author_delete')]
    public function delete(EntityManagerInterface $mr, $id): Response
    {
        $author = $mr->getRepository(Author::class)->find($id);
        $mr->remove($author);
        $mr->flush();

        return $this->redirectToRoute('author_getAuthors');
    }
}
