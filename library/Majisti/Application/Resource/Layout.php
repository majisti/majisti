<?php

namespace Majisti\Application\Resource;

class Layout extends \Zend_Application_Resource_Layout
{
    /**
     * Retrieve layout object
     *
     * @return Zend_Layout
     */
    public function getLayout()
    {
        $options = $this->getOptions();

        if( !isset($options['pluginClass']) ) {
            $options['pluginClass'] = "Majisti\Controller\Plugin\LayoutSwitcher";
            $this->setOptions($options);
        }

        return parent::getLayout();
    }
}