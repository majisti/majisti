<?php

namespace Majisti\Application\Resource;

/**
 * @desc This resource will register a LayoutSwitcher plugin
 * that will enable modules with their own layout views.
 *
 * @author Majisti
 */
class Layout extends \Zend_Application_Resource_Layout
{
    /**
     * @desc Retrieves the layout class and appends it to the pluginClass
     * option it it was never set. This will enable layout views for
     * each modules.
     *
     * @return \Zend_Layout
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
