<?php

namespace MajistiX\Editing\View\Editor;

/**
 * @desc An editor is a component used to render a content model.
 * The editor should be able to render the content given to it. Even if
 * no content is given, it should render itself so that content can be
 * posted. The editor should provide post or AJAX methods to post values
 * that will be used for insertion later on.
 *  
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
interface IEditor
{
    /**
     * @desc Renders the editor with the given content.
     * 
     * @param \MajistiX\Extension\Editing\Model\Content The content
     */
    public function render(\MajistiX\Editing\Model\Content $content);

    public function setOptions(array $options);

    public function preset($preset);

    static public function preload(array $options);
}
