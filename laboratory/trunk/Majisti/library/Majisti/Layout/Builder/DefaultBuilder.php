<?php

namespace Majisti\Layout\Builder;

/**
 * TODO: Utility class (or view helper) for generating all kind of HTML elements so that no
 * HTML code is withing PHP code, only OOP.
 * 
 * @author Steven Rosato
 */
class DefaultBuilder extends BuilderAbstract
{
    public function buildHead($dom)
    {
        $head = '';
        
        $head .= $this->getView()->headLink()->prependStylesheet(BASE_URL . '/styles/core.css');
        //$this->headLink()->prependStylesheet(LIB_URL . '/css/common.css')
        $head .= $this->getView()->headLink()->prependStylesheet(MAJISTI_URL . '/styles/layouts/majisti/default.css');
        
        $head .= $this->getView()->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');
        
        $head .= $this->getView()->headMeta();
        $head .= $this->getView()->headStyle();
        
        //TODO: use internationalised modules/controllers/actions name for default title generation
        $head .= $this->getView()->headTitle('Majisti');
        
        $head . $this->getView()->headScript();
        
        return "<head>{$head}</head>{$dom}";
        
    }
    
    public function buildBody(IBuilderBody $bodyBuilder, $dom)
    {
        return "{$dom}<body>{$bodyBuilder->buildAll()}</body>";
    }
}