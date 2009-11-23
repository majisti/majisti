<?php

namespace Majisti\I18n;

require_once 'TestHelper.php';

/**
 * @desc
 * @author Steven Rosato
 */
class LocaleSessionTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;
    
    protected $_i18n;
    
    /**
     * Setups the test case
     */
    public function setUp()
    {
        \Zend_Registry::set('Majisti_Config', new \Zend_Config(array(
        	'plugins' => array('i18n' => array(
        		'requestParam' 			=> 'lang',
        		'defaultLocale' 		=> 'en',
        		'supportedLocales'		=> array('fr', 'es')
        	))
        )), true);
        
        $this->_i18n = LocaleSession::getInstance();
    }
    
    public function tearDown()
    {
    	if( null !== $this->_i18n ) {
	    	$this->_i18n->reset();
    	}
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
    	
    	$i18n = new I18n();
    	$this->assertEquals('fr', $i18n->getCurrentLocale());
    	
    	$i18n->switchLocale('es');
    	$i18n = new I18n();
    	$this->assertEquals('es', $i18n->getCurrentLocale());
    	
    	$i18n->switchLocale();
    	$i18n = new I18n();
    	$this->assertEquals('en', $i18n->getCurrentLocale());
    }
    
    public function testReset()
    {
    	$this->markTestIncomplete();
    }
}

LocaleSessionTest::runAlone();
