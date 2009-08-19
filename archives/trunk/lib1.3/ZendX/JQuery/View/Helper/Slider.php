<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category    ZendX
 * @package     ZendX_JQuery
 * @subpackage  View
 * @copyright   Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license     http://framework.zend.com/license/new-bsd     New BSD License
 * @version     $Id: Slider.php 11941 2008-10-13 19:41:38Z matthew $
 */

/**
 * @see ZendX_JQuery_View_Helper_UiWidget
 */
require_once "ZendX/JQuery/View/Helper/UiWidget.php";

/**
 * jQuery Slider View Helper
 *
 * @uses 	   Zend_Json
 * @package    ZendX_JQuery
 * @subpackage View
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendX_JQuery_View_Helper_Slider extends ZendX_JQuery_View_Helper_UiWidget
{
	
		/**
		 * The Slider's event names
		 *
		 * @var array
		 */
		protected $_eventNames = array('start', 'slide', 'change', 'stop');
	
    /**
     * Create jQuery slider that updates its values into a hidden form input field.
     *
     * @link   http://docs.jquery.com/UI/Slider
     * @param  string $id
     * @param  string $value
     * @param  array  $params
     * @param  array  $attribs
     * @return string
     */
    public function slider($id, $value = null, array $params = array(), array $attribs = array())
    {
        if(!isset($attribs['id'])) {
            $attribs['id'] = $id;
        }

        $jqh = ZendX_JQuery_View_Helper_JQuery::getJQueryHandler();

        $handleCount = 1;
        if(isset($params['handles']) && is_array($params['handles'])) {
            $handleCount = min(count($params['handles']), 1);
        }
        if(!isset($params['handles'][0]['start'])) {
            if(is_numeric($value)) {
                $params['value'] = $value;
            } elseif(!isset($params['startValue'])) {
                //$params['value'] = 1;
            }
        }

        // Build the Change/Update functionality of the Slider via javascript, updating hidden fields. aswell as hidden fields
        $hidden = "";
        if(!isset($params['change'])) {
        	$sliderUpdateFnName = "zfjSliderUpdate".preg_replace('/([^a-zA-Z0-9]+)/', '', $attribs['id']);
        	$sliderUpdateFn = sprintf('function %s(e, ui) {'.PHP_EOL, $sliderUpdateFnName);
            for($i = 0; $i < $handleCount; $i++) {
                // Js Func
                if($i === 0) {
                    $sliderHiddenId = $attribs['id'];
                } else {
                    $sliderHiddenId = $attribs['id']."-".$i;
                }
                $sliderUpdateFn .= sprintf('    %s("#%s").attr("value", ui.value);'.PHP_EOL,
                    $jqh, $sliderHiddenId, $jqh, $attribs['id'], $i);

                // Hidden Fields
                $startValue = (isset($params['handles'][$i]['start']))?$params['handles'][$i]['start']:$params['startValue'];
                $hiddenAttribs = array('type' => 'hidden', 'id' => $sliderHiddenId, 'name' => $sliderHiddenId, 'value' => $startValue);
                $hidden .= '<input' . $this->_htmlAttribs($hiddenAttribs) . $this->getClosingBracket(). PHP_EOL;
            }
            $sliderUpdateFn .= "}".PHP_EOL;
            $params['change'] = $sliderUpdateFnName;

            $this->jquery->addJavascript($sliderUpdateFn);
        }
        
        $stringToObject = array();
        foreach ($this->_eventNames as $eventName) {
        	if ( isset($params[$eventName]) ) {
        		$stringToObject[$params[$eventName]] = str_replace('"', '\\"', $params[$eventName]);
        	}
        }

        $attribs['id'] .= "-slider";

        if(count($params) > 0) {
            /**
             * @see Zend_Json
             */
            require_once "Zend/Json.php";
            $params = Zend_Json::encode($params);
            //$params = str_replace('"'.$sliderUpdateFnName.'"', $sliderUpdateFnName, $params);
            foreach ($stringToObject as $str_replace => $str) {
            	$params = str_replace('"'.$str.'"',$str_replace,$params);
            }
        } else {
            $params = '{}';
        }

        $js = sprintf('%s("#%s").slider(%s);', $jqh, $attribs['id'], $params);
        $this->jquery->addOnLoad($js);

        $html = '<div' . $this->_htmlAttribs($attribs) . '>';
        for($i = 0; $i < $handleCount; $i++) {
            $html .= '<div class="ui-slider-handle"></div>';
        }
        $html .= '</div>';

        return $hidden.$html;
    }
}