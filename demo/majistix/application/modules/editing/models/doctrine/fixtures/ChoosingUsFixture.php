<?php

namespace MajistiX\Editing\Model\Doctrine\Fixtures;

use \Doctrine\Common\DataFixtures,
    \MajistiX\Editing\Model\Content;

require_once 'phpQuery.php';

class ChoosingUsFixture implements DataFixtures\FixtureInterface
{
    public function load($manager)
    {
        \phpQuery::newDocument();

        $lorem  = new \Majisti\Util\Model\LoremIpsumGenerator();
        $locale = new \Zend_Locale('en');

        /* intro */
        $content = new Content('choosingUs_introduction', $locale);
        $content->setContent($lorem->getContent(1000));
        $manager->persist($content);

        /* be unique */
        $content = new Content('choosingUs_unique', $locale);
        $content->setContent(\pq('<div />')
            ->addClass('choosingUs_unique')
            ->append(pq('<img />')
                //FIXME: how to differ from production/dev values???
                ->attr('src', '/majisti-0.4.0alpha2/demo/majistix/public/images/be-unique.jpg')
                ->attr('alt', 'be-unique')
            )
            ->append(\pq('<div />')
                ->append(\pq('<h3 />')
                    ->html("Be unique!")
                )
                //TODO: use YAML??
                ->append(\pq('<p />')
                    ->html("Majisti Solutions guarantees you an awesome and unique look. Your visual layout will be built by our graphic designers, according to your tastes. Many companies will offer you to choose from a set of premade templates that they buy from stock companies, we do not.")
                )
                ->append(\pq('<p />')
                    ->html("That does not mean we do not follow popular trends. After all, it's up to you to decide what image you are willing to display online, whichever it might be!")
                )
            )
        );
        $manager->persist($content);

        /* flush */
        $manager->flush();
    }
}
