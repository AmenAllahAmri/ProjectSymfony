<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BookController extends AbstractController
{

    #[Route('/addBook', name:"addBook")]
    public function add(Request $request, ManagerRegistry $doctrine){
        $book=new Book();
        $form=$this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $book->setPublished(true);
            $author = $book->getAuthor();
            $author->setNbBooks($author->getNbBooks() + 1);

            $em=$doctrine->getManager();
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('ShowAllBook');
        }

            return $this->render('book/add.html.twig', ['formB' => $form->createView()]);
    }

    #[Route('/book', name: 'ShowAllBook')]
    public function list(ManagerRegistry $doctrine): Response
    {
        $repo = $doctrine->getRepository(\App\Entity\Book::class);

        $publishedBooks = $repo->findBy(['published' => true]);

        $countPublished = count($repo->findBy(['published' => true]));
        $countUnpublished = count($repo->findBy(['published' => false]));

        return $this->render('book/list.html.twig', [
            'books' => $publishedBooks,
            'countPublished' => $countPublished,
            'countUnpublished' => $countUnpublished,
        ]);
    }

}
