<?php

namespace MajistiT\Application;

/**
 * @desc The application's bootstrap
 *
 * @author Majisti
 */
class Bootstrap extends \Majisti\Application\Bootstrap
{
    /**
     * @desc Runs the bootstrap
     */
    public function run()
    {
        /* short tags for view scripts */
        $this->getResource('view')->setUseStreamWrapper(true);

        $front = $this->getResource('FrontController');
        $front->registerPlugin(new \MajistiT\Plugin\Main());

        require_once 'phpQuery.php';
        \phpQuery::newDocumentXHTML();

        parent::run();
    }
}
