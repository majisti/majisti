<?php

namespace MajistiX\Extensions\InPlaceEditing\Model;

/**
 * @desc InPlaceEditing storage model facade that will render or edit content
 * according to its set editor and storage model.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class InPlaceEditing extends \Majisti\Model\Storage\StorableModel
{
    /**
     * @var string
     */
    protected $_genericStorage = 'MajistiX\Extensions\InPlaceEditing\Model\Storage\IStorage';

    /**
     * @var Editor\IEditor
     */
    protected $_editor;

    /**
     * @desc Construcs the InPlaceEditing with a storage model and an editor.
     *
     * @param $storageModel The storage model
     * @param $editor The editor
     */
    public function __construct($storageModel, Editor\IEditor $editor)
    {
        parent::__construct($storageModel);
        $this->setEditor($editor);
    }

    /**
     * @desc Returns the editor.
     *
     * @return Editor\IEditor The editor
     */
    public function getEditor()
    {
        return $this->_editor;
    }

    /**
     * @desc Sets the editor.
     *
     * @param Editor\IEditor $editor The editor
     *
     * @return InPlaceEditing this
     */
    public function setEditor(Editor\IEditor $editor)
    {
        $this->_editor = $editor;

        return $this;
    }

    /**
     * @desc Returns the content of the storage model
     * from a given key and locale.
     *
     * @param $key The key
     * @param $locale [optionnal] The locale to fetch, if no locale is specified
     * the application's current locale is used.
     *
     * @return string The content
     */
    public function getContent($key, $locale = null)
    {
        return $this->getStorageModel()->getContent( (string) $key,
            $this->_getLocale($locale));
    }

    /**
     * @desc Edits the content of the storage model
     * from a given key, content and locale.
     *
     * @param $key The key
     * @param $content The content
     * @param $locale [optionnal] The locale. If no locale is specified the
     * application's current locale is used.
     *
     * @throws Majisti\Model\Storage\Exception if the storage model is read-only.
     *
     * @return InPlaceEditing this
     */
    public function editContent($key, $content, $locale = null)
    {
        $this->getStorageModel()->editContent((string)$key, (string)$content,
            $this->_getLocale($locale));

        return $this;
    }

    /**
     * @desc Renders the content using the editor with the key and locale provided.
     *
     * @param $key The key
     * @param $locale [optionnal] The local. If no locale is specified the
     * applications's current locale is used.
     */
    public function render($key, $locale = null)
    {
        return $this->getEditor()->render($this->getContent($key, $locale),
            array('key' => $key));
    }

    /**
     * @desc Returns the current locale if the locale is null.
     *
     * @param string $locale
     *
     * @return string The locale
     */
    protected function _getLocale($locale)
    {
        if( null === $locale ) {
            $locale = \Majisti\I18n\LocaleSession::getInstance()->getCurrentLocale();
        }

        return (string) $locale;
    }
}
