<?php

namespace MajistiX\Extension\Editing\View\Helper;

use Majisti\Application\Locales,
    MajistiX\Extension\Editing\View\Editor;

/**
 * @desc InPlaceEditing view helper. Renders the default in place content editor
 * setup in the configuration. Check documentation for more information on how
 * to configure a in place editor.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Editing extends \Majisti\View\Helper\AbstractHelper
{
    protected $_inPlaceEditingModel;

    protected $_editor;

    /**
     * @desc Renders content based on storage key.
     *
     * @param $key The storage key
     * @param $options the options
     */
    public function helper($key, $options = array())
    {
        $editor = Editor\Provider::getInstance()->provideEditor();

        if( !is_array($options) ) {
            $editor->preset($options);
        } else {
            $editor->setOptions($options);
        }

        return $editor->render($this->getModel($key));
    }

    /**
     * @return \MajistiX\Model\Editing\InPlace
     */
    protected function getModel($key)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = \Zend_Registry::get('Doctrine_EntityManager');

        $repo = $em->getRepository(
            'MajistiX\Extension\Editing\Model\Content');
        $model = $repo->findOrCreate($key,
            Locales::getInstance()->getCurrentLocale());

        return $model;
    }
}