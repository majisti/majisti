<?php

/**
 * TODO: doc
 * 
 * @author Steven Rosato
 */
class Majisti_Form_Decorator_Label extends Zend_Form_Decorator_Label
{
	protected $_markedAsRequired;
	
	protected $_tagClass;
	
	public function isMarkedAsRequired()
	{
		if( null === $this->_markedAsRequired && ($option = $this->getOption('markedAsRequired')) !== null ) {
			$this->_markedAsRequired = $option;
		}
		
		return $this->_markedAsRequired;		
	}
	
	public function setMarkedAsRequired($boolFlag = true)
	{
		$this->_markedAsRequired = $boolFlag;		
	}
	
	public function getTagClass()
	{
		if( null === $this->_tagClass && null !== $this->getOption('tagClass') ) {
			$this->_tagClass = $this->getOption('tagClass');
		}
		
		return $this->_tagClass;	
	}
	
	public function setTagClass($tagClass)
	{
		$this->_tagClass = $tagClass;
	}
	
	public function getLabel()
	{
		$reqPrefix = $this->getReqPrefix();
        $reqSuffix = $this->getReqSuffix();
        $translator = $this->getElement()->getTranslator();
        
        $tooltip = $translator == null ? 'Mendatory field' : $translator->translate('mendatory_tooltip');
        
        if( empty($reqPrefix) && empty($reqSuffix) ) {
        	$this->setReqPrefix('<span title="'. 
        		 $tooltip .
        		'" class="required">*&nbsp;</span>');
			$this->setOption('escape', false);
        }
		
		if (null === ($element = $this->getElement())) {
            return '';
        }

        $label = $element->getLabel();
        $label = trim($label);

        if (empty($label)) {
            return '';
        }

        if (null !== ($translator = $element->getTranslator())) {
            $label = $translator->translate($label);
        }

        $optPrefix = $this->getOptPrefix();
        $optSuffix = $this->getOptSuffix();
        $reqPrefix = $this->getReqPrefix();
        $reqSuffix = $this->getReqSuffix();
        $separator = $this->getSeparator();

        if (!empty($label)) {
            if ($element->isRequired() || $this->isMarkedAsRequired() ) {
                $label = $reqPrefix . $label . $reqSuffix;
            } else {
                $label = $optPrefix . $label . $optSuffix;
            }
        }
        
        $this->removeOption('markedAsRequired');

        return $label;
	}
	
    /**
     * @desc Render a label just the same as a Zend_Form_Decorator_Label would,
     * but adding the avaibility to add classes to the HtmlTag decorator
     * wrapped around that label but giving 'tagClass' => 'class[es]'
     * in the options
     * 
     * @param  string $content 
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }
        
        /* Now supports tag class for the label's decorator */
        $tagClass = $this->getTagClass();
        $this->removeOption('tagClass');

        $label     = $this->getLabel();
        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $tag       = $this->getTag();
        $id        = $this->getId();
        $class     = $this->getClass();
        $options   = $this->getOptions();
        
        if (empty($label) && empty($tag)) {
            return $content;
        }

        if (!empty($label)) {
            $options['class'] = $class;
            $label = $view->formLabel($element->getFullyQualifiedName(), trim($label), $options); 
        } else {
            $label = '&nbsp;';
        }

        if (null !== $tag) {
            require_once 'Zend/Form/Decorator/HtmlTag.php';
            $decorator = new Zend_Form_Decorator_HtmlTag();
            $decorator->setOptions(array('tag' => $tag));
            
            if( null !== $tagClass ) {
            	$decorator->setOption('class', $tagClass);
            }
            
            $label = $decorator->render($label);
        }

        switch ($placement) {
            case self::APPEND:
                return $content . $separator . $label;
            case self::PREPEND:
                return $label . $separator . $content;
        }
    }
}