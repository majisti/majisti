<?php

namespace MajistiT\Application;

/**
 * @desc The application's bootstrap
 *
 * @author Majisti
 */
class Bootstrap extends \Majisti\Application\Bootstrap
{
    /*
     * (non-phpDoc) 
     * @see Inherited documentation.
     */
    protected function _bootstrap($resource = null)
    {
        parent::_bootstrap($resource);

        require_once 'phpQuery.php';
        \phpQuery::newDocumentXHTML();
    }

    /*
     * (non-phpDoc) 
     * @see Inherited documentation.
     */
    public function run()
    {
        /* short tags for view scripts */
        $this->getResource('view')->setUseStreamWrapper(true);

        $front = $this->getResource('FrontController');
        $front->registerPlugin(new \MajistiT\Plugin\Main());

        parent::run();
    }
}
