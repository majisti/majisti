<?php

namespace Majisti\Config;

/**
 * @desc The configuration class for extending multiple
 * options sets with a default set. Basically, this class is used
 * as the main dependency injection configuration component for all classes
 * that need configuration. The behaviour of retrieving or checking against
 * present option values is the responsability of this object. Classes
 * won't need to receive ad-hoc options arrays that they handle individually but
 * instead manipulate options and default options through this instance.
 *
 * This object may or may not provide default options.
 * This object may or may not provide options.
 * It should at least provide default options or options,
 * but this class does not verify this.
 * Retrieving options from this object will always return the options provided
 * after they were merged with the default options by overriding them
 * recursively.
 *
 * @author Steven Rosato
 */
class Configuration
{
    /**
     * @var \Zend_Config
     */
    protected $_defaultOptions;

    /**
     * @var \Zend_Config
     */
    protected $_options;

    /**
     * @desc Constructs the configuration object.
     *
     * @param \Zend_Config|Configuration|array $options The options (optionnal)
     * @param \Zend_Config|Configuration|array $defaultOptions
     *      (optionnal) The default options
     */
    public function __construct($options = array(), $defaultOptions = array())
    {
        $this->setDefaultOptions($defaultOptions);
        $this->extend($options);
    }

    /**
     * @desc Sets the default options.
     *
     * @param \Zend_Config|Configuration|array $options
     * @return Configuration This
     */
    public function setDefaultOptions($options)
    {
        $options = $this->formatOptions($options);
        $this->_defaultOptions = $options;

        return $this;
    }

    /**
     * @desc Returns a clone of the default options, read only.
     *
     * @return \Zend_Config The default options
     */
    public function getDefaultOptions()
    {
        $defaultOptions = clone $this->_defaultOptions;
        $defaultOptions->setReadOnly();

        return $defaultOptions;
    }

    /**
     * @desc Extends the current options with the ones provided.
     *
     * @param \Zend_Config|Configuration|array $options The options
     *
     * @return Configuration Provides fluent interface
     */
    public function extend($options)
    {
        $options = $this->formatOptions($options);

        $this->_options = null === $this->_options
            ? $options
            : $this->_options->merge($options);

        return $this;
    }

    /**
     * @desc Clears the options.
     *
     * @return Configuration Provides fluent interface
     */
    public function clearOptions()
    {
        $this->_options = null;

        return $this;
    }

    /**
     * @desc Queries the options for the a CSS like selection.
     * @param string|array $selection The selection or an array of selection.
     *
     * @return bool True if the selection (or all the selection are present
     * in the configuration
     *
     * @see Selector for CSS like selection explaination.
     */
    public function has($selection)
    {
        if( !is_array($selection) ) {
            $selection = array($selection);
        }

        foreach( $selection as $value ) {
            if( null === $this->find((string)$value, null) ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @desc Returns the options.
     *
     * @return \Zend_Config The options
     */
    public function getOptions()
    {
        $options = clone $this->_defaultOptions;

        if( null !== $this->_options ) {
            $options->merge($this->_options);
        }

        $options->setReadOnly();

        return $options;
    }

    /**
     * @desc Proxy call for Selector::find() function.
     * 
     * @see {@link Selector::find()}
     */
    public function find($selection, $returnDefault = Selector::VOID,
        $returnConfigAsArray = false)
    {
        $selector = new Selector($this->getOptions());
        return $selector->find($selection, $returnDefault, $returnConfigAsArray);
    }

    /**
     * @desc Formats the options to a usable \Zend_Config ready
     * for modifications. The options can be a Configuration, Zend_Config
     * object (locked or not) or an array.
     *
     * @param \Zend_Config|Configuration|array $options The options
     *
     * @return \Zend_Config
     */
    protected function formatOptions($options)
    {
        if( $options instanceof Configuration ) {
            $options = $options->getOptions()->toArray();
        }

        if( !($options instanceof \Zend_Config ^ is_array($options)) ) {
            throw new Exception("Options must be an instance" . 
                " of Zend_Config, Configuration or array");
        }

        if( $options instanceof \Zend_Config && $options->readOnly() ) {
            $options = $options->toArray();
        }

        if( is_array($options) ) {
            $options = new \Zend_Config(
                $options,
                array('allowModifications' => true)
            );
        }

        return $options;
    }
}
