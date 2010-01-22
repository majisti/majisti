<?php

namespace Majisti\View\LayoutBuilder;

abstract class BuilderAbstract extends \Majisti\Util\Model\ViewAggregate implements IBuilder
{
    /** @var IBuilderBody */
    protected $_builderBody;
    
    public function __construct($builderBody = null)
    {
        $this->_builderbody = $builderBody;
    }
    
    /**
     *
     * @return IBuilderBody
     */
    public function getBuilderBody()
    {
        if( null === $this->_builderBody ) {
            $this->_builderBody = new DefaultBuilderBody();
        }
        
        return $this->_builderBody;
    }
    
    public function setBuilderBody(IBuilderBody $builderBody)
    {
        $this->_builderBody = $builderBody;
    }
    
    public function buildAll()
    {
        $dom = '';
        
        $dom = $this->buildHead($dom);
        
        $bodyBuilder = $this->getBuilderBody();
        $bodyBuilder->setView($this->getView());
        
        $dom = $this->buildBody($bodyBuilder, $dom);
        $dom = $this->buildHtml($dom);
        $dom = $this->buildDoctype($dom);
        
        return $dom;
    }
    
    public function buildDoctype($dom)
    {
        return $this->getView()->doctype() . $dom;
    }
    
    public function buildHtml($dom)
    {
        return '<html>' . $dom . '</html>';
    }
    
    public function buildHead($dom)
    {
        return '<head><title>Majisti</title></head>' . $dom;
    }
}