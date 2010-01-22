<?php

namespace Majisti\View\LayoutBuilder;

class DefaultBuilderBody extends BuilderBodyAbstract implements IBuilderBody
{
    public function buildHeader($content)
    {
        return $content . '<div class="header"><div class="banner"></div></div>';
    }
    
    public function buildContent($content)
    {
        return $content
            . '<div class="content">'
            . $this->getView()->layout()->content
            . '</div>'
            . $this->getView()->inlineScript();
    }
    
    public function buildFooter($content)
    {
        return $content . '<div class="footer">Footer</div>';
    }
}