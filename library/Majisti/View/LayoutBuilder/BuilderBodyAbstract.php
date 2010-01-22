<?php

namespace Majisti\View\LayoutBuilder;

abstract class BuilderBodyAbstract extends \Majisti\Util\Model\ViewAggregate implements IBuilderBody
{
    public function buildAll()
    {
        $content = '';
        
        $content = $this->buildHeader($content);
        $content = $this->buildContent($content);
        $content = $this->buildFooter($content);
        $content = $this->buildContainer($content);
        
        return $content;
    }
    
    public function buildContainer($content)
    {
        return '<div class="container">' . $content . '</div>';
    }
}