<?php

namespace Majisti\Application;

require_once __DIR__ . '/TestHelper.php';

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

        $this->locales = new Locales('Foo');
        $this->locales->setLocales($this->mainLocales);
        $this->locales->reset();
    }

    /**
     * @desc Restarts the session, but does not clear it.
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
    	$locales = $this->locales;
    	
    	/* test that the locale object behaves according to the settings */
    	$this->assertEquals($this->en, $locales->getDefaultLocale());
    	$this->assertEquals($this->mainLocales, $locales->getLocales());
    	$this->assertTrue($locales->isCurrentLocaleDefault());
    	$this->assertTrue($locales->hasLocale($this->en));
    	$this->assertTrue($locales->hasLocale($this->fr));
    	$this->assertTrue($locales->hasLocale($this->es));
    	
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
    	$locales = $this->locales;
    	
    	$this->assertEquals($this->en, $locales->getDefaultLocale());
    	
    	$locales->switchLocale($this->fr);
    	$this->assertEquals($this->en, $locales->getDefaultLocale());
    	
    	$locales->switchLocale($this->es);
    	$this->assertEquals($this->en, $locales->getDefaultLocale());
    }
    
    /**
     * Tests that switchLocale modifies current locale.
     */
	public function testSwitchLocale()
    {
        foreach( $this->mainLocales as $key => $locale ) {
            $this->locales->switchLocale($locale);
            $this->assertEquals($this->mainLocales[$key],
                    $this->locales->getCurrentLocale());
        }
    }

    /**
     * @expectedException Exception
     */
    public function testSwitchingToNonExistantLocaleThrowsException()
    {
    	$this->locales->switchLocale($this->de);
    }
    
    /**
     * Tests the current locale persistence in the session.
     */
    public function testCurrentLocalePersistance()
    {
    	$locales = $this->locales;
    	$locales->switchLocale($this->fr);
    	
    	$this->restartSession();
    	
    	$locales = new Locales('Foo');
    	$this->assertEquals($this->fr, $locales->getCurrentLocale());
    	
    	$this->restartSession();
    	
    	$locales->switchLocale($this->es);
    	$locales = new Locales('Foo');
    	$this->assertEquals($this->es, $locales->getCurrentLocale());
    	
    	$this->restartSession();
    	
    	$locales->switchLocale($this->en);
    	$this->assertEquals($this->en, $locales->getCurrentLocale());
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
        $locales  = $this->locales;

        $locales->addLocales(array($this->de, $this->it));
        $this->assertTrue($locales->hasLocale($this->de));
        $this->assertTrue($locales->hasLocale($this->it));

        $locales->switchLocale($this->de);
        $this->assertEquals($this->de, $locales->getCurrentLocale());
        $this->assertEquals($this->en, $locales->getDefaultLocale());

        $locales->addLocale($this->ca);
        $this->assertTrue($locales->hasLocale($this->ca));
    }

    /**
     * Tests that removing one or multiple locales behaves as expected.
     */
    public function testRemoveLocale()
    {
       $mainLocales = $this->mainLocales;
       $locales     = $this->locales;

       $this->assertFalse($locales->removeLocale($this->ca));
       $this->assertFalse($locales->hasLocale($this->ca));

       $locales->addLocales(array($this->de, $this->it, $this->ca));
       $count   = $locales->count();

       $locales->removeLocale($this->it);
       $this->assertFalse($locales->hasLocale($this->it));
       $this->assertEquals(--$count, $locales->count());

       $locales->removeLocales(array($this->de, $this->ca));
       $this->assertFalse($locales->hasLocale($this->de));
       $this->assertFalse($locales->hasLocale($this->ca));
       $this->assertEquals($count - 2, $locales->count());

       $this->assertEquals($this->mainLocales, $mainLocales);
    }

    /**
     * Tests that the setLocales function overrides all locales.
     */
    public function testThatSetLocalesOverwritesAllLocales()
    {
        $mainLocales = $this->mainLocales;
        $locales     = $this->locales;
        $altLocales  = $this->altLocales;

        $locales->setLocales($altLocales);
        $this->assertEquals($altLocales, $locales->getLocales());
        $this->assertTrue($locales->hasLocales($altLocales));
        $this->assertEquals(count($altLocales), $locales->count());
        $this->assertEquals($this->de, $locales->getDefaultLocale());
        $this->assertEquals($this->de, $locales->getCurrentLocale());

        $locales->setLocales($mainLocales);
        $this->assertEquals($mainLocales, $locales->getLocales());
        $this->assertTrue($locales->hasLocales($mainLocales));
        $this->assertEquals(count($mainLocales), $locales->count());
        $this->assertEquals($this->en, $locales->getDefaultLocale());
        $this->assertEquals($this->en, $locales->getCurrentLocale());

        $locales->setLocales(array());
        $this->assertTrue($locales->isEmpty());
        $this->assertFalse($locales->hasLocales($mainLocales));
        $this->assertNull($locales->getDefaultLocale());
        $this->assertNull($locales->getCurrentLocale());
    }

    /**
     * Tests that the default locale setter behaves as expected.
     * @expectedException Exception
     */
    public function testThatSetDefaultLocaleChangesDefaultLocale()
    {
        $locales = $this->locales;
        $fr     = $this->fr;
        $it     = $this->it;

        $locales->setDefaultLocale($fr);
        $this->assertEquals($fr, $locales->getDefaultLocale());

        /* will throw exception */
        $locales->setDefaultLocale($it);
    }

    /**
     * Tests that the current locale setter behaves as expected.
     * @expectedException Exception
     */
    public function testThatSetCurrentLocaleChangesCurrentLocale()
    {
        $locales = $this->locales;
        $fr     = $this->fr;
        $it     = $this->it;

        $locales->switchLocale($fr);
        $this->assertEquals($fr, $locales->getCurrentLocale());

        /* will throw exception */
        $locales->switchLocale($it);
    }

    /**
     * Tests that the isCurrentLocaleDefault() function returns true
     * only if the current locale is also the default one.
     */
    public function testIsCurrentLocaleDefault()
    {
        $locales = $this->locales;
        $fr      = $this->fr;
        $en      = $this->en;

        $locales->switchLocale($fr);
        $locales->setDefaultLocale($fr);
        $this->assertTrue($locales->isCurrentLocaleDefault());

        $locales->switchLocale($en);
        $this->assertFalse($locales->isCurrentLocaleDefault());
    }
}

LocalesTest::runAlone();
