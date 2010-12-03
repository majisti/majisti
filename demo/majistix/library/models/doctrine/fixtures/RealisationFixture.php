<?php

namespace MyApp\Model\Doctrine\Fixtures;

use \Doctrine\Common\DataFixtures,
    \MyApp\Model\Realisation;

class RealisationFixture implements DataFixtures\FixtureInterface
{
    public function load($manager)
    {
        $realisation = new Realisation();

        $manager->persist($realisation);

        $manager->flush();
    }
}