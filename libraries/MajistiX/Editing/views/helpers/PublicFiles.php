<?php

namespace MajistiX\Editing\View\Helper;

use Majisti\Config\Configuration;

class PublicFiles extends \Majisti\View\Helper\AbstractHelper
{
    public function helper(Configuration $publicFiles)
    {
        $view = $this->view;

        if( $publicFiles->has('scripts') ) {
            foreach( $publicFiles->find('scripts') as $jsFile ) {
                $view->headScript()->appendFile($jsFile);
            }
        }

        if( $publicFiles->has('styles') ) {
            foreach( $publicFiles->find('styles') as $cssFile ) {
                $view->headLink()->appendStylesheet($cssFile);
            }
        }
    }
}