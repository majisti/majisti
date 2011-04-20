<?php

namespace Majisti\Test;

use Majisti\Application as Application,
    Majisti\Test\Util\ServerInfo,
    Doctrine\ORM\EntityManager
;

/**
 * @desc The test case serves as a simplified manner to extend PHPUnit TestCases.
 * It provides support for single running a test or running it as a part
 * of a TestSuite.
 * 
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class FunctionalTestCase extends \Zend_Test_PHPUnit_ControllerTestCase
                         implements Test
{
    /**
     * @var \Zend_Application 
     */
    public $bootstrap;

    /**
     * @var boolean 
     */
    static protected $_databaseCreated = false;

    /**
     * @var boolean
     */
    static protected $_useDatabase = false;

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    static public function setUpBeforeClass()
    {
        if( static::$_useDatabase && !static::$_databaseCreated ) {
            $helper = \Majisti\Test\Helper::getInstance();

            if( !static::$_databaseCreated ) {
                $helper->getDatabaseHelper()->recreateSchema();
                static::$_databaseCreated = true;
            }

            $helper->getDatabaseHelper()->reloadFixtures();
        }
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function setUp()
    {
        $this->reset();

        /* Zend's bootstrap takes a Zend_Application */
        $this->bootstrap = $this->getHelper()->createApplicationInstance();

        $this->bootstrap->bootstrap();
        $this->_frontController = $this->bootstrap->getBootstrap()
             ->getResource('frontController');

        $this->frontController
             ->setRequest($this->getRequest())
             ->setResponse($this->getResponse());

        /*
         * notifies observers that the entity manager changed
         */
        if( static::$_useDatabase &&
            $this->getDatabaseHelper() instanceof Database\DoctrineHelper )
        {
            $em = $this->getDatabaseHelper()->getEntityManager();

            //FIXME: use observer pattern (event manager) instead..
            \Zend_Registry::set('Doctrine_EntityManager', $em);
            $this->bootstrap
                 ->getBootstrap()
                 ->getContainer()
                 ->doctrine = $em;

            //FIXME: ad-hoc implementation!
            if( \Zend_Registry::isRegistered('Symfony_DependencyInjection_Container') ) {
                \Zend_Registry::get('Symfony_DependencyInjection_Container')
                    ->set('doctrine.em', $em);
            }
        }

        $this->getHelper()->setApplication($this->bootstrap);
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    static public function tearDownAfterClass()
    {
        if( static::$_useDatabase && static::$_databaseCreated ) {
            \Majisti\Test\Helper::getInstance()
                ->getDatabaseHelper()->reloadFixtures();
        }
    }

    /**
     * Returns the database helper for this helper.
     * 
     * @return Database\Helper 
     */
    public function getDatabaseHelper()
    {
        return $this->getHelper()->getDatabaseHelper();
    }

    /**
     * Returns the bootstrap created by this functional test.
     */
    public function getBootstrap()
    {
        return $this->getApplication()->getBootstrap();
    }

    /**
     * Returns the created application for this functional test.
     *
     * @return \Zend_Application
     */
    public function getApplication()
    {
        return $this->bootstrap;
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function dispatch($url = null)
    {
        // redirector should not exit
        $redirector = \Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        $redirector->setExit(false);

        // json helper should not exit
        $json = \Zend_Controller_Action_HelperBroker::getStaticHelper('json');
        $json->suppressExit = true;

        $request = $this->getRequest();
        if (null !== $url) {
            $request->setRequestUri($url);
        }
        $request->setPathInfo(null);

        $controller = $this->getFrontController();
        $this->frontController
             ->setRequest($request)
             ->setResponse($this->getResponse())
             ->returnResponse(false);

        if ($this->bootstrap instanceof \Zend_Application) {
            $this->bootstrap->run();
        } else {
            $this->frontController->dispatch();
        }
    }

    /**
     * @desc Runs a test alone.
     *
     * @param array $arguments [opt; def=Runner's default] The Runner's arguments
     */
    static public function runAlone($arguments = array())
    {
        Standalone::runAlone(get_called_class(), $arguments);
    }

    /**
     * @desc Returns the helper instance
     *
     * @return Helper
     */
    public function getHelper()
    {
        return \Majisti\Test\Helper::getInstance();
    }
}
