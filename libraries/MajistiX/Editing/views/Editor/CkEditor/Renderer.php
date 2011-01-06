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

        return new Configuration(array(
            'scripts' => array(
                'ckeditor'          => $ckeditorUrl . '/ckeditor.js',
                'ckeditor-jquery'   => $ckeditorUrl . '/adapters/jquery.js',
                'ckeditor-concrete' => $jsUrl       . '/scripts/ckeditor.js'
            )
        ));
    }
}
