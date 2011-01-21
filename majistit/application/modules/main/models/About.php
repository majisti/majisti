<?php

namespace MyApp\Main\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @desc About page
 */
class About
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $_em;

    /**
     * @column(type="string")
     *
     * @var html
     */
    private $mission;

    /**
     * @desc Constructs the about page
     */
    public function __construct()
    {
        $this->_em = \Zend_Registry::get('Doctrine_EntityManager');
    }

    /**
     * @desc Returns the mission statement
     *
     * @return html The company's mission
     */
    public function getMission()
    {
        return $this->mission;
    }

    /**
     * @return ArrayCollection
     */
    public function getRealisations()
    {
        return $this->_em->getRepository('MyApp\Model\Realisation')->findAll();
    }
}
