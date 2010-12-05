<?php

namespace Majisti\View;

/**
 * @desc The core view class adding more behaviour to the traditional
 * {@link \Zend_View} by providing the underscore function for traduction and
 * the support for multiple fallback directories for view scripts (according
 * to the multiple dispatcher).
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class View extends \Zend_View 
{
    /**
     * @var mixed
     */
    protected $_return;
    
    /**
     * @var bool 
     */
    protected $_enableOutput;

    /*
     * (non-phpDoc) 
     * @see Inherited documentation.
     * Workaround for ZF-7907. Namespaced view helpers
     * must use the helper function for proxy invocation.
     */
    public function __call($name, $args)
    {
        $helper = $this->getHelper($name);

        if ( method_exists($helper, $name) ) {
            $methodName = $name;
        } else {
            $methodName = 'helper';
        }

        return call_user_func_array(array($helper, $methodName), $args);
    }

    /**
     * @desc Traduction function that proxies to the translate view helper.
     * 
     * @param string $messageId The message to translate
     * @param string|Zend_Locale $locale (Optional)
     * 
     * @return string The translated message, or this messageId if no translation was found
     */
    public function _($messageId, $locale = null)
    {
        if (null === $locale) {
            return $this->getHelper('Translate')->translate($messageId);
        } else {
            return $this->getHelper('Translate')->translate($messageId, $locale);
        }
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function addBasePath($path, $classPrefix = 'Zend_View')
    {
        $path        = rtrim($path, '/');
        $path        = rtrim($path, '\\');
        $path       .= DIRECTORY_SEPARATOR;
        $classPrefix = str_replace('_', '\\', $classPrefix);
        $classPrefix = rtrim($classPrefix, '\\') . '\\';
        $this->addScriptPath($path . 'scripts');
        $this->addHelperPath($path . 'helpers', $classPrefix . 'Helper\\');
        $this->addFilterPath($path . 'filters', $classPrefix . 'Filter\\');
        return $this;
    }
    
    /**
     * @desc Returns whether if a translator is registered with this view
     * @return boolean True if a translator is registered.
     */
    public function hasTranslator()
    {
        return \Zend_Registry::isRegistered('Zend_Translate');
    }
    
    /**
     * @desc Sets the translator
     * @param \Zend_Translate $translate The translator
     * @return View this
     */
    public function setTranslator(\Zend_Translate $translate)
    {
        \Zend_Registry::set('Zend_Translate', $translate);
        return $this;
    }
    
    /**
     * @desc Returns the translator
     * @return \Zend_Translate
     */
    public function getTranslator()
    {
        if( $this->hasTranslator() ) {
            return \Zend_Registry::get('Zend_Translate');
        }
        return null;
    }

    /**
     * @desc Optimizes the headlink.
     *
     * @param string $path The master file path
     * @param string $url The master file url
     * @param array $options The options for the optimizer
     *
     * @return \Majisti\View\Helper\Head\HeadLinkOptimizer The optimizer
     *
     * @see \Majisti\View\Helper\Head\HeadLinkOptimizer
     */
    public function optimizeHeadLink($path, $url, array $options = array())
    {
        $optimizer = new View\Helper\Head\HeadLinkOptimizer($this, $options);
        $optimizer->optimize($path, $url);

        return $optimizer;
    }

    /**
     * @desc Optimizes the headscript.
     *
     * @param string $path The master file path
     * @param string $url The master file url
     * @param array $options The options for the optimizer
     *
     * @return \Majisti\View\Helper\Head\HeadScriptOptimizer The optimizer
     *
     * @see Majisti\View\Helper\Head\HeadScriptOptimizer
     */
    public function optimizeHeadScript($path, $url, array $options = array())
    {
        $optimizer = new View\Helper\Head\HeadScriptOptimizer($this, $options);
        $optimizer->optimize($path, $url);

        return $optimizer;
    }

    /**
     * @desc Fallbacks to the Dispatcher's controller directories whenever
     * a script is not found in the default controller directory, provided that
     * the front controller's dispatcher is an instance of 
     * Majisti\Dispatcher\IDispatcher
     * 
     * @param $name The script name to find
     * @return string The script name
     */
    protected function _script($name)
    {
        try {
            return parent::_script($name);
        } catch( \Zend_View_Exception $e ) {
            $front      = \Zend_Controller_Front::getInstance();
            $dispatcher = $front->getDispatcher();
            $request    = $front->getRequest();

            /* fallback to the dispatcher's controller directories */
            if( null !== $request &&
                $dispatcher instanceof \Majisti\Controller\Dispatcher\IDispatcher )
            {
                $fallbacks = $dispatcher->getFallbackControllerDirectory(
                    $request->getModuleName());

                if( null !== $fallbacks ) {
                    foreach ($fallbacks as $fallback) {
                        $path       = $fallback[1];

                        $script = realpath($path
                            . '/../views/scripts/' . $name);
                        if( is_readable($script) ) {
                            return $script;
                        }
                    }
                }
            }

            throw $e;
        }
    }

    /**
     * @desc Sets a mixed value that will be returned when the render()
     * function will be called instead of an output buffered capture.
     * This is particularly usefull when a view
     * is not outputing anything but rather preparing a certain object
     * without using the placeholders and is returning that
     * prepared object to the caller. Note that you can still enable the output,
     * but the ouput will be printed barely and the object will be returned after,
     * meaning that output buffering capture should still be used in those
     * specific use cases.
     *
     * @param mixed $return The returned value
     * @param bool $enableOutput [opt] Enable output using a simple print
     * when rendering.
     *
     * @return \Majisti\View this
     */
    public function setRenderReturn($return, $enableOutput = true)
    {
        $this->_return       = $return;
        $this->_enableOutput = $enableOutput;

        return $this;
    }

    /**
     * @desc Returns whether the view will return a value upon
     * render() call.
     *
     * @return bool True if the view contains a return value.
     */
    public function hasRenderReturn()
    {
        return null !== $this->_return;
    }

    /**
     * @desc Clears the view's render value.
     *
     * @return \Majisti\View this
     */
    public function clearRenderReturn()
    {
        $this->_return = null;

        return $this;
    }

    /**
     * @desc Returns the view's render value.
     *
     * @return mixed Returns the value that gets returned
     * upon render() call.
     */
    public function getRenderReturn()
    {
        return is_object($this->_return)
            ? clone $this->_return
            : $this->_return;
    }

    /**
     * @desc Renders normally unless setRenderReturn() was previouslly called
     * in which case the view still gets rendered, but the prepared value will
     * be returned instead of an output buffered capture.
     *
     * @param string $name
     * @return mixed
     */
    public function render($name)
    {
        $render = parent::render($name);

        if( $this->hasRenderReturn() ) {
            if( $this->_enableOutput ) {
                print $render;
            }
            $return = $this->getRenderReturn();
            $this->clearRenderReturn();

            return $return;
        }

        return $render;
    }
}
