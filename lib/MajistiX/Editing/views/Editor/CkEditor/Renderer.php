<?php

namespace MajistiX\Editing\View\Editor\CkEditor;

use MajistiX\Editing\View\Editor\AbstractEditor,
    Majisti\Config\Configuration;

/**
 * @desc Editor implementation of the very popular CkEditor.
 * Configuration of the editor is issued through of list of params
 * available in the documentation.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Renderer extends AbstractEditor
{
    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function getPublicFiles(Configuration $majisti)
    {
        $jsUrl       = $majisti->find('url') . '/majistix/editing';
        $ckeditorUrl = $jsUrl . '/editors/ckeditor';

        /* concrete editor always loaded last */
        $key = 'majistix-editing-100-';

        return new Configuration(array(
            'scripts' => array(
                "{$key}editor-1" => $ckeditorUrl . '/ckeditor.js',
                "{$key}editor-2" => $ckeditorUrl . '/adapters/jquery.js',
                "{$key}editor-3" => $jsUrl       . '/scripts/editor/ckeditor.js'
            )
        ));
    }
}
