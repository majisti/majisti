<?php

namespace MyApp\Model\Doctrine\Fixtures;

use \Doctrine\Common\DataFixtures,
    \MyApp\Model as Model;

class BookFixture implements DataFixtures\FixtureInterface
{
    public function load($manager)
    {
        $book = new Model\Book();
        $book->setTitle('A fixture book');
        $book->setPublicationYear(2010);

        $article = new Model\Article();
        $article->setTitle('A fixture article');

        $book->addArticle($article);

        $manager->persist($book);
        $manager->flush();
    }
}
