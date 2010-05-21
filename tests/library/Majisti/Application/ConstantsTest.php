<?php

namespace Majisti\Application;

require_once 'TestHelper.php';

/**
 * @desc Tests that application constants are defined correctly
 * with correct values.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class ConstantsTest extends \Majisti\Test\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @var string
     */
    public $applicationPath;

    /**
     * @var \Zend_Controller_Request_Http
     */
    public $request;

    /**
     * Setups the test case
     */
    public function setUp()
    {
        $this->applicationPath = dirname(__FILE__) . '/_project/application';
        \Zend_Registry::set('Majisti_Config', new \Zend_Config(array()));
        $this->request = new \Zend_Controller_Request_Http();
    }

    /**
     * @desc Asserts that constants are correctly defined
     * and that their values match exactly as the expected value.
     *
     * @param array $constants Constants to assert
     * as an array of name => value pairs
     */
    protected function assertConstants(array $constants)
    {
        foreach ($constants as $name => $value) {
            $this->assertTrue(defined($name),
                "Constant {$name} not defined");
            $this->assertEquals($value, constant($name),
                "Constant {$name} value's is incorrect");
        }
    }

    /**
     * @return Returns the expected static constants
     */
    public function getExpectedConstants()
    {
        $req = $this->request;

        return array(
            'APPLICATION_PATH'          => $this->applicationPath,
            'APPLICATION_ENVIRONMENT'   => 'development',
            'APPLICATION_LIBRARY'       => APPLICATION_LIBRARY,

            /*
             * getcwd provides with /home/user/www/.... and we have to keep only
             * the chunk starting from /majisti.
             */
            'BASE_URL'                  => $req->getBaseUrl(),
            'MAJISTI_ROOT'              => realpath(dirname(dirname(dirname
                                           (__FILE__))) . '/../..'),
            'MAJISTI_LIBRARY'           => MAJISTI_ROOT . '/library',
            'MAJISTI_PATH'              => MAJISTI_LIBRARY . '/Majisti',
            'MAJISTIX_PATH'             => MAJISTI_LIBRARY . '/MajistiX',
            'MAJISTIX_MODULES'          => MAJISTIX_PATH . '/Modules',
            'MAJISTIX_EXTENSIONS'       => MAJISTIX_PATH . '/Extensions',
            'APPLICATION_URL_PREFIX'    => $req->getScheme() . '://' .
                                           $req->getHttpHost() ,
            'APPLICATION_URL'           => APPLICATION_URL_PREFIX . BASE_URL,
            /* going accordingly to the biased BASE_URL */
            'APPLICATION_URL_STYLES'    => $req->getBaseUrl() . '/styles',
            'APPLICATION_URL_SCRIPTS'   => $req->getBaseUrl() . '/scripts',
            'APPLICATION_URL_IMAGES'    => $req->getBaseUrl() .
                                           '/images/common'
        );
    }

    /**
     * @return Returns the expected static constants
     */
    public function getExpectedConfigurableConstants()
    {
        $req = $this->request;

        return array(
            'MAJISTI_PUBLIC'                => $req->getScheme() . '://' .
                                               $req->getHttpHost() . '/' .
                                               MAJISTI_FOLDER_NAME . '/public',
            'MAJISTI_URL'                   => $req->getScheme() . '://' .
                                               $req->getHttpHost() . '/' .
                                               MAJISTI_FOLDER_NAME . '/public/majisti',
            'MAJISTIX_URL'                  => $req->getScheme() . '://' .
                                               $req->getHttpHost() . '/' .
                                               MAJISTI_FOLDER_NAME . '/public/majistix',
            'MAJISTI_URL_STYLES'            => MAJISTI_URL . '/styles',
            'MAJISTI_URL_SCRIPTS'           => MAJISTI_URL . '/scripts',
            'MAJISTI_URL_IMAGES'            => MAJISTI_URL . '/images/common',
            /* locale for Majisti and projects is en */
            'MAJISTI_URL_IMAGES_LOCALE'     => MAJISTI_URL . '/images/en',
            'APPLICATION_URL_IMAGES_LOCALE' => BASE_URL . '/images/en',
            'APPLICATION_LOCALE_CURRENT'    => 'en',
            'APPLICATION_LOCALE_DEFAULT'    => 'en',
            'MAJISTIX_URL_IMAGES'           => MAJISTIX_URL . '/images/common',
            'MAJISTIX_URL_STYLES'           => MAJISTIX_URL . '/styles',
            'MAJISTIX_URL_SCRIPTS'          => MAJISTIX_URL . '/scripts',
            'JQUERY'                        => MAJISTI_PUBLIC . '/jquery',
            'JQUERY_UI'                     => MAJISTI_PUBLIC . '/jquery/ui',
            'JQUERY_PLUGINS'                => MAJISTIX_URL . '/jquery/plugins',
            'JQUERY_STYLES'                 => MAJISTIX_URL . '/jquery/styles',
            'JQUERY_THEMES'                 => MAJISTIX_URL . '/jquery/themes'
        );
    }

    public function getExpectedAliases()
    {
        $expectedConstants          = $this->getExpectedConstants();
        $expectedConfigConstants    = $this->getExpectedConfigurableConstants();

        return array(
            'APP_PATH'      => $expectedConstants['APPLICATION_PATH'],
            'APP_LIB'       => $expectedConstants['APPLICATION_LIBRARY'],
            'APP_ENV'       => $expectedConstants['APPLICATION_ENVIRONMENT'],
            'APP_PREFIX'    => $expectedConstants['APPLICATION_URL_PREFIX'],
            'APP_URL'       => $expectedConstants['APPLICATION_URL'],
            'APP_SCRIPTS'   => $expectedConstants['APPLICATION_URL_SCRIPTS'],
            'APP_STYLES'    => $expectedConstants['APPLICATION_URL_STYLES'],
            'APP_IMG'       => $expectedConstants['APPLICATION_URL_IMAGES'],
            'APP_IMG_LOC'   => $expectedConfigConstants[
                                            'APPLICATION_URL_IMAGES_LOCALE'],
            'APP_LANG'      => $expectedConfigConstants[
                                            'APPLICATION_LOCALE_CURRENT'],
            'APP_LANG_DEF'  => $expectedConfigConstants[
                                            'APPLICATION_LOCALE_DEFAULT'],
            'MAJ_ROOT'      => $expectedConstants['MAJISTI_ROOT'],
            'MAJ_LIB'       => $expectedConstants['MAJISTI_LIBRARY'],
            'MAJ_PATH'      => $expectedConstants['MAJISTI_PATH'],
            'MAJ_PUB'       => $expectedConfigConstants['MAJISTI_PUBLIC'],
            'MAJX_PATH'     => $expectedConstants['MAJISTIX_PATH'],
            'MAJX_EXT'      => $expectedConstants['MAJISTIX_EXTENSIONS'],
            'MAJX_MOD'      => $expectedConstants['MAJISTIX_MODULES'],
            'MAJ_URL'       => $expectedConfigConstants['MAJISTI_URL'],
            'MAJX_URL'      => $expectedConfigConstants['MAJISTIX_URL'],
            'MAJ_STYLES'    => $expectedConfigConstants['MAJISTI_URL_STYLES'],
            'MAJ_SCRIPTS'   => $expectedConfigConstants['MAJISTI_URL_SCRIPTS'],
            'MAJ_IMG'       => $expectedConfigConstants['MAJISTI_URL_IMAGES'],
            'MAJ_IMG_LOC'   => $expectedConfigConstants[
                                            'MAJISTI_URL_IMAGES_LOCALE'],
            'MAJX_STYLES'   => $expectedConfigConstants['MAJISTIX_URL_STYLES'],
            'MAJX_SCRIPTS'  => $expectedConfigConstants['MAJISTIX_URL_SCRIPTS'],
            'MAJX_IMG'      => $expectedConfigConstants['MAJISTIX_URL_IMAGES'],
            'JQ'            => $expectedConfigConstants['JQUERY'],
            'JQ_PLUGINS'    => $expectedConfigConstants['JQUERY_PLUGINS'],
            'JQ_STYLES'     => $expectedConfigConstants['JQUERY_STYLES'],
            'JQ_THEMES'     => $expectedConfigConstants['JQUERY_THEMES']
        );
    }

    public function testConstantsAreAllDefinedCorrectly()
    {
        $this->assertConstants($this->getExpectedConstants());
    }

    public function testConfigurableConstantsAreAllDefinedCorrectly()
    {
        $this->assertConstants($this->getExpectedConfigurableConstants());
    }

    public function testAliasesAreAllDefinedCorrectly()
    {
        $this->assertConstants($this->getExpectedAliases());
    }

    public function testAliasesDisabled()
    {
        $this->assertTrue(Constants::isAliasesUsed());
        Constants::setUseAliases(false);
        $this->assertFalse(Constants::isAliasesUsed());
    }
}

ConstantsTest::runAlone();
