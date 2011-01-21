<?php

use MyApp\Model\Book,
    MyApp\Model\Article,
    Symfony\Component\DependencyInjection;

/**
 * @desc The index controller.
 *
 * @author Majisti
 */
class IndexController extends Zend_Controller_Action
{
    /**
     * @desc The index action
     */
    public function indexAction()
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->_helper->doctrine();

        $book = new Book();
        $book->setTitle('A new book');
        $book->setPublicationYear(2009);

        $em->persist($book);

        $article = new Article();
        $article->setTitle("A new article title");

        $book->addArticle($article);

        $em->flush();

        /* @var $repo \MyApp\Model\BookRepository */
        $repo = $em->getRepository('MyApp\Model\Book');
        $books = $repo->getRecentBooks();

        $this->books = $books;
    }

    public function fooAction()
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->_helper->doctrine();
        /* @var $rep \MyApp\Model\BookRepository */
        $rep = $em->getRepository('MyApp\Model\Book');

        /* @var $books \Doctrine\ORM\AbstractQuery */
        $books = $rep->getRecentBooks();

        /* @var $articles \Doctrine\Common\Collections\ArrayCollection */
        $articles = $books[0]->getArticles();

        \Zend_Debug::dump($articles->toArray());
    }

    public function realisationsAction()
    {
        $about = new \MyApp\Main\Model\About();
        $realisations = $about->getRealisations();
        \Zend_Debug::dump($realisations);
    }
}
