<?php

namespace Majisti\I18n;

require_once 'TestHelper.php';

/**
 * @desc
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class LocaleSessionTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;
    
    /**
     * @var LocaleSession
     */ 
    protected $_i18n;
    
    /* default i18n configuration */
    protected $_i18nConfig = array(
       'plugins' => array('i18n' => array(
       'requestParam'      => 'lang',
       'defaultLocale'     => 'en',
       'supportedLocales'  => array('fr', 'es')
    )));
    
    /**
     * Setups the test case
     */
    public function setUp()
    {
        \Zend_Session::start();
        
        \Zend_Registry::set('Majisti_Config', 
            new \Zend_Config($this->_i18nConfig, true));
        
        $this->_i18n = LocaleSession::getInstance();
        $this->_i18n->reset();
    }
    
    protected function _restartSession()
    {
        \Zend_Session::writeClose();
        \Zend_Session::start();
    }
    
    public function test__construct()
    {
    	$i18n = $this->_i18n;
    	
    	/* test that the I18n object behaves according to the configuration */
    	$this->assertEquals('en', $i18n->getDefaultLocale());
    	$this->assertEquals(array('en', 'fr', 'es'), $i18n->getLocales());
    	$this->assertEquals(array('fr', 'es'), $i18n->getSupportedLocales());
    	$this->assertTrue($i18n->isCurrentLocaleDefault());
    	$this->assertTrue($i18n->isLocaleSupported('en'));
    	$this->assertTrue($i18n->isLocaleSupported('fr'));
    	$this->assertTrue($i18n->isLocaleSupported('es'));
    	
    	/* test that the Zend_Locale was setup correctly in the registry */
    	$locale = \Zend_Registry::get('Zend_Locale');
    	$defaultLocale = $locale->getDefault();
    	$this->assertNotNull($locale);
    	$this->assertEquals('en', key($defaultLocale));
    	$this->assertEquals('en', $locale->getLanguage());
    }
    
    public function testGetDefaultLocale()
    {
    	$i18n = $this->_i18n;
    	
    	$this->assertEquals('en', $i18n->getDefaultLocale());
    	
    	$i18n->switchLocale();
    	$this->assertEquals('en', $i18n->getDefaultLocale());
    	
    	$i18n->switchLocale('es');
    	$this->assertEquals('en', $i18n->getDefaultLocale());
    }
    
    /**
     * @expectedException Exception
     */
	public function testSwitchLocale()
    {
    	$i18n = $this->_i18n;
    	
    	$locale = $i18n->switchLocale();
    	$this->assertEquals('fr', $locale);
    	$this->assertEquals('fr', $i18n->getCurrentLocale());
    	
    	$locale = $i18n->switchLocale();
    	$this->assertEquals('es', $locale);
    	$this->assertEquals('es', $i18n->getCurrentLocale());
    	
    	$locale = $i18n->switchLocale();
    	$this->assertEquals('en', $locale);
    	$this->assertEquals('en', $i18n->getCurrentLocale());
    	
    	$locale = $i18n->switchLocale('es');
    	$this->assertEquals('es', $locale);
    	$this->assertEquals('es', $i18n->getCurrentLocale());
    	
    	/* throws exception */
    	$i18n->switchLocale('de');
    }
    
    
    public function testCurrentLocalePersistance()
    {
    	$i18n = $this->_i18n;
    	$i18n->switchLocale();
    	
    	$this->_restartSession();
    	
    	$i18n = LocaleSession::getInstance();
    	$this->assertEquals('fr', $i18n->getCurrentLocale());
    	
    	$this->_restartSession();
    	
    	$i18n->switchLocale('es');
    	$i18n = LocaleSession::getInstance();
    	$this->assertEquals('es', $i18n->getCurrentLocale());
    	
    	$this->_restartSession();
    	
    	$i18n->switchLocale();
    	$this->assertEquals('en', $i18n->getCurrentLocale());
    }
}

LocaleSessionTest::runAlone();
