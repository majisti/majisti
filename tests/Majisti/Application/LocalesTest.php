<?php

namespace Majisti\Application;

require_once 'TestHelper.php';

/**
 * @desc Tests that the locale session can switch session through multiple
 * session instances.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class LocalesTest extends \Majisti\Test\TestCase
{
    static protected $_class = __CLASS__;
    
    /**
     * @var Locales
     */
    public $locales;
    
    /**
     *  @var \Zend_Locale
     */
    public $en;

    /**
     * @var \Zend_Locale
     */
    public $fr;

    /**
     * @var \Zend_Locale
     */
    public $es;

    /**
     * @var \Zend_Locale
     */
    public $de;

    /**
     * @var \Zend_Locale
     */
    public $it;

    /**
     * @var \Zend_Locale
     */
    public $ca;

    /**
     * @var array
     */
    public $mainLocales;
    
    /**
     * @var array of \Zend_Locale
     */
    public $altLocales;

    /**
     * @desc Setups the test case
     */
    public function setUp()
    {
        $this->restartSession();

        $this->en = new \Zend_Locale('en');
        $this->fr = new \Zend_Locale('fr');
        $this->es = new \Zend_Locale('es');
        $this->de = new \Zend_Locale('de');
        $this->it = new \Zend_Locale('it');
        $this->ca = new \Zend_Locale('ca');

        $this->mainLocales  = array($this->en, $this->fr, $this->es);
        $this->altLocales   = array($this->de, $this->it, $this->ca);

        $this->locales = Locales::getInstance();
        $this->locales->setLocales($this->mainLocales);
        $this->locales->reset();
    }

    /**
     * @desc Restarts the session
     */
    protected function restartSession()
    {
        \Zend_Session::writeClose();
        \Zend_Session::start();
    }

    /**
     * Tests that the object build with the \Zend_Locale array sets the
     * current and default locale to the first array index.
     *
     * Tests that other locales are available.
     */
    public function test__construct()
    {
    	$locale = $this->locales;
    	
    	/* test that the locale object behaves according to the settings */
    	$this->assertEquals($this->en, $locale->getDefaultLocale());
    	$this->assertEquals($this->mainLocales, $locale->getLocales());
    	$this->assertTrue($locale->isCurrentLocaleDefault());
    	$this->assertTrue($locale->hasLocale($this->en));
    	$this->assertTrue($locale->hasLocale($this->fr));
    	$this->assertTrue($locale->hasLocale($this->es));
    	
    	/* test that the Zend_Locale was setup correctly in the registry */
    	$localeSession = \Zend_Registry::get('Zend_Locale');
    	$this->assertNotNull($localeSession);
    	$this->assertEquals($this->en, $localeSession);
    }

    /**
     * Tests the getDefault() function behaviour when switching a locale.
     */
    public function testGetDefault()
    {
    	$locale = $this->locales;
    	
    	$this->assertEquals($this->en, $locale->getDefaultLocale());
    	
    	$locale->switchLocale($this->fr);
    	$this->assertEquals($this->en, $locale->getDefaultLocale());
    	
    	$locale->switchLocale($this->es);
    	$this->assertEquals($this->en, $locale->getDefaultLocale());
    }
    
    /**
     * Tests that switchLocale modifies current locale.
     * @expectedException Exception
     */
	public function testSwitchLocale()
    {
        foreach( $this->mainLocales as $key => $locale ) {
            $switch = $this->locales->switchLocale($locale);
            $this->assertEquals($locale, $switch);
            $this->assertEquals($this->mainLocales[$key],
                    $this->locales->getCurrentLocale());
        }
    	
    	/* throws exception */
    	$this->locales->switchLocale($this->de);
    }
    
    /**
     * Tests the current locale persistence in the session.
     */
    public function testCurrentLocalePersistance()
    {
    	$locale = $this->locales;
    	$locale->switchLocale($this->fr);
    	
    	$this->restartSession();
    	
    	$locale = Locales::getInstance();
    	$this->assertEquals($this->fr, $locale->getCurrentLocale());
    	
    	$this->restartSession();
    	
    	$locale->switchLocale($this->es);
    	$locale = Locales::getInstance();
    	$this->assertEquals($this->es, $locale->getCurrentLocale());
    	
    	$this->restartSession();
    	
    	$locale->switchLocale($this->en);
    	$this->assertEquals($this->en, $locale->getCurrentLocale());
    }

    /**
     * Tests that the reset function sets the current locale value to the
     * default's one.
     */
    public function testThatResetWillSetCurrentLocaleToTheDefaultOne()
    {
        $this->locales->reset();
        $this->assertEquals($this->locales->getCurrentLocale(),
                $this->locales->getDefaultLocale());
    }

    /**
     * Tests that get locales works with exclude default
     */
    public function testGetLocales()
    {
        $locales    = $this->locales;
        $allLocales = $locales->getLocales();

        $this->assertEquals($this->locales, $locales);

        $locales->setDefaultLocale($this->en);
        unset($allLocales[0]); //remove en

        $this->assertEquals($allLocales, $locales->getLocales(true));
    }

    /**
     * Tests that adding a locale or multiple locales behaves like expected.
     */
    public function testAddLocale()
    {
        $locales = $this->mainLocales;
        $locale  = $this->locales;

        $locale->addLocales(array($this->de, $this->it));
        $this->assertTrue($locale->hasLocale($this->de));
        $this->assertTrue($locale->hasLocale($this->it));

        $locale->switchLocale($this->de);
        $this->assertEquals($this->de, $locale->getCurrentLocale());
        $this->assertEquals($this->en, $locale->getDefaultLocale());

        $locale->addLocale($this->ca);
        $this->assertTrue($locale->hasLocale($this->ca));
    }

    /**
     * Tests that removing one or multiple locales behaves as expected.
     */
    public function testRemoveLocale()
    {
       $locales = $this->mainLocales;
       $locale  = $this->locales;

       $this->assertFalse($locale->removeLocale($this->ca));
       $this->assertFalse($locale->hasLocale($this->ca));

       $locale->addLocales(array($this->de, $this->it, $this->ca));
       $count   = $locale->count();

       $locale->removeLocale($this->it);
       $this->assertFalse($locale->hasLocale($this->it));
       $this->assertEquals(--$count, $locale->count());

       $locale->removeLocales(array($this->de, $this->ca));
       $this->assertFalse($locale->hasLocale($this->de));
       $this->assertFalse($locale->hasLocale($this->ca));
       $this->assertEquals($count - 2, $locale->count());

       $this->assertEquals($this->mainLocales, $locales);
    }

    /**
     * Tests that the setLocales function overrides all locales.
     */
    public function testThatSetLocalesOverwritesAllLocales()
    {
        $locales    = $this->mainLocales;
        $locale     = $this->locales;
        $altLocales = $this->altLocales;

        $locale->setLocales($altLocales);
        $this->assertEquals($altLocales, $locale->getLocales());
        $this->assertTrue($locale->hasLocales($altLocales));
        $this->assertEquals(count($altLocales), $locale->count());
        $this->assertEquals($this->de, $locale->getDefaultLocale());
        $this->assertEquals($this->de, $locale->getCurrentLocale());

        $locale->setLocales($locales);
        $this->assertEquals($locales, $locale->getLocales());
        $this->assertTrue($locale->hasLocales($locales));
        $this->assertEquals(count($locales), $locale->count());
        $this->assertEquals($this->en, $locale->getDefaultLocale());
        $this->assertEquals($this->en, $locale->getCurrentLocale());

        $locale->setLocales(array());
        $this->assertTrue($locale->isEmpty());
        $this->assertFalse($locale->hasLocales($locales));
        $this->assertNull($locale->getDefaultLocale());
        $this->assertNull($locale->getCurrentLocale());
    }

    /**
     * Tests that the default locale setter behaves as expected.
     * @expectedException Exception
     */
    public function testThatSetDefaultLocaleChangesDefaultLocale()
    {
        $locale = $this->locales;
        $fr     = $this->fr;
        $it     = $this->it;

        $locale->setDefaultLocale($fr);
        $this->assertEquals($fr, $locale->getDefaultLocale());

        /* will throw exception */
        $locale->setDefaultLocale($it);
    }

    /**
     * Tests that the current locale setter behaves as expected.
     * @expectedException Exception
     */
    public function testThatSetCurrentLocaleChangesCurrentLocale()
    {
        $locale = $this->locales;
        $fr     = $this->fr;
        $it     = $this->it;

        $locale->switchLocale($fr);
        $this->assertEquals($fr, $locale->getCurrentLocale());

        /* will throw exception */
        $locale->switchLocale($it);
    }

    /**
     * Tests that the isCurrentLocaleDefault() function returns true
     * only if the current locale is also the default one.
     */
    public function testIsCurrentDefaultFunction()
    {
        $locale = $this->locales;
        $fr     = $this->fr;
        $en     = $this->en;

        $locale->switchLocale($fr);
        $locale->setDefaultLocale($fr);
        $this->assertTrue($locale->isCurrentLocaleDefault());

        $locale->switchLocale($en);
        $this->assertFalse($locale->isCurrentLocaleDefault());
    }
}

LocalesTest::runAlone();
