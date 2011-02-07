<?php

namespace MajistiX\Editing\Model;

use \Doctrine\Common\DataFixtures,
    \MajistiX\Editing\Model\Content;

class ContentFixture implements DataFixtures\FixtureInterface
{
    public function load($manager)
    {
        $lorem = new \Majisti\Util\Model\LoremIpsumGenerator();

        $content = new Content('content1', new \Zend_Locale('en'));
        $content->setContent($lorem->getContent(20));

        $manager->persist($content);

        $content = new Content('content2', new \Zend_Locale('en'));
        $content->setContent($lorem->getContent(20));

        $manager->persist($content);

        $manager->flush();
    }
}