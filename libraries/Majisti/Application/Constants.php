<?php

namespace Majisti\Application;

/**
 * @desc Class that declares constants needed in standard applications.
 * It supports short names aliases as well by default.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Constants
{
    /**
     * @var bool
     */
    static protected $_aliasesUsed;

    /**
     * @desc Private constructor for no instanciation
     */
    private function __construct()
    {}

    /**
     * @desc Defines all dynamic constants that will be built
     * according to configuration.
     */
    static public function defineConfigurableConstants()
    {
    }

    /**
     * @desc Whether aliases should be defined or not. Will
     * lazily checks the configuration for majisti.app.useConstantsAliases
     * to determine if constants aliases should be defined. If no such
     * configuration is found, aliases will be defined by default.
     *
     * @return True if aliases should be used
     */
    static public function isAliasesUsed()
    {
        if( null === static::$_aliasesUsed ) {
            $selector = new \Majisti\Config\Selector(
                \Zend_Registry::get('Majisti_Config'));
            static::$_aliasesUsed = (bool)$selector->find(
                'majisti.app.useConstantsAliases', true);
        }

        return static::$_aliasesUsed;
    }

    /**
     * @desc Whether this application should define aliases.
     *
     * @param bool $useAliases
     */
    static public function setUseAliases($useAliases)
    {
        static::$_aliasesUsed = (bool)$useAliases;
    }

    /**
     * @desc Defines aliases for the previous defined constants.
     */
    static public function defineAliases()
    {
        if( static::isAliasesUsed() ) {
        }
    }
}
