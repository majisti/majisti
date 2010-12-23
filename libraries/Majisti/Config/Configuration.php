<?php

namespace Majisti\Config;

class Configuration
{
    protected $_defaultOptions;

    protected $_options;

    public function __construct($options, $defaultOptions = array())
    {
        if ( empty($defaultOptions) ) {
            $defaultOptions = $options;
            $options = array();
        }

        $this->setDefaultOptions($defaultOptions);

        if( empty($options) ) {
            $options = $defaultOptions;
        }

        $this->extend($options);
    }

    public function setDefaultOptions($options)
    {
        $options = $this->formatOptions($options);
        $this->_defaultOptions = $options;

        return $this;
    }

    public function getDefaultOptions()
    {
        return new \Zend_Config($this->_defaultOptions->toArray());
    }

    public function extend($options)
    {
        $defaultOptions = new \Zend_Config($this->_defaultOptions->toArray(),
            array('allowModifications' => true));

        $options = $defaultOptions->merge($this->formatOptions($options));

        $this->_options = null === $this->_options
            ? $options
            : $this->_options->merge($options);

        return $this;
    }

    public function reset()
    {
        $this->_options = null;
        $this->extend($this->getDefaultOptions());

        return $this;
    }

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

    public function getOptions()
    {
        return new \Zend_Config($this->_options->toArray());
    }

    public function find($selection, $returnDefault = Selector::VOID,
        $returnConfigAsArray = false)
    {
        $selector = new Selector($this->getOptions());

        return $selector->find($selection, $returnDefault, $returnConfigAsArray);
    }

    protected function formatOptions($options)
    {
        if( is_array($options) ) {
            $options = new \Zend_Config(
                $options,
                array('allowModifications' => true)
            );
        } elseif( $options instanceof Configuration ) {
            $options = $options->getOptions();
        } elseif( !($options instanceof \Zend_Config) ) {
            throw new \Exception("Options must be an instance of Zend_Config");
        }

        return $options;
    }
}
