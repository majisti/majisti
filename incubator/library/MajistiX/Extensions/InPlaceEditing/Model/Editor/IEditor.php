<?php

namespace MajistiX\Extensions\InPlaceEditing\Model\Editor;

/**
 * @desc An editor is a component used to render InPlaceEditing content.
 * The editor should be able to render the content given to it. Even if
 * no content is given, it should render itself so that content can be
 * posted. The editor should provide post or AJAX methods to post values
 * that will be used for insertion later on.
 *  
 * @author Steven Rosato
 */
interface IEditor
{
    /**
     * @desc Renders the editor with the given content.
     * 
     * @param $content The content
     * @param $params The params
     */
    public function render($content, array $params = array());
}
