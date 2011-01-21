<?php

namespace MyApp\Model\Doctrine\Fixtures;

use \Doctrine\Common\DataFixtures,
    \MyApp\Model\Article;

class ArticleFixture implements DataFixtures\FixtureInterface
{
    public function load($manager)
    {
        $article = new Article();
        $article->setTitle('An unattached article');

        $manager->persist($article);
        $manager->flush();
    }
}