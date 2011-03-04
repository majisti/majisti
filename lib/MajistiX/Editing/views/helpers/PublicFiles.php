<?php

namespace MajistiX\Editing\View\Helper;

use Majisti\Config\Configuration;

class PublicFiles extends \Majisti\View\Helper\AbstractHelper
{
    public function helper(Configuration $publicFiles)
    {
        $view = $this->view;

        if( $publicFiles->has('scripts') ) {
            foreach( $publicFiles->find('scripts') as $name => $jsFile ) {
                if( is_int($name) ) {
                    $view->headScript()->appendFile($jsFile);
                } else {
                    $view->headScript()->offsetSetFile($name, $jsFile);
                }
            }
        }

        if( $publicFiles->has('styles') ) {
            foreach( $publicFiles->find('styles') as $name => $cssFile ) {
                if( is_int($name) ) {
                    $view->headLink()->appendStylesheet($cssFile);
                } else {
                    $view->headLink()->offsetSetStylesheet($name, $cssFile);
                }
            }
        }
    }
}