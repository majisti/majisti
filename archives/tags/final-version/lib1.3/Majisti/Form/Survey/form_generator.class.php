<?php

/**
 * Class constants naming : Marco Roy
 * Class conception : Steven Rosato & Marco Roy
 * Functions programming : Steven Rosato
 * 
 * Every comments are in english for better flexibily
 * even though both programmers are originally french ;)
 * @version 1.2
 * @deprecated DEPRECATED, only here to serve as a plan for Majisti_Survey_Generator
 */


/***************************************************** 
 ****************** Class constants ******************
 *****************************************************/

/********************** PARAMS **********************/
/**
 * @desc Puts queries to the left with left alignment,
 * checkboxes will be added to the right with left alignment, 1% width
 */
define('QUERY_LL', 0);

/**
 * @desc Puts queries to the left with right alignment,
 * checkboxes will be added to the right with left alignment, 1% width
 */
define('QUERY_LR', 1);

/**
 * @desc Puts queries to the right with left alignment,
 * checkboxes will be added to the left with left alignment, 1% width
 */
define('QUERY_RL', 2);

/**
 * @desc Puts queries to the right with right alignment,
 * checkboxes will be added to the left with left alignment, 1% width
 */
define('QUERY_RR', 3);

define('QUERY_UD', 4);
define('QUERY_DU', 5);
/****************************************************/

/********************** HTML **********************/
define('div', '<div>');
define('divE', '</div>');
define('divC', '<div class="');
/** Table tag with default CSS class */
define('table', '<table>');
/** Table tag with custom CSS class */
define('tableC', '<table class="');
/** Table end tag */
define('tableE', '</table>');

/** Table row tag with default CSS class */
define('tr', '<tr>');
/** Table row with custom CSS class */
define('trC', '<tr class="');
/** Table row end tag */
define('trE', '</tr>');

/** Table data tag with default CSS class */
define('td', '<td>');
/** Table data with custom CSS class */
define('tdC', '<td class="');
/** Table data end tag */
define('tdE', '</td>');
/** Table data column spanning */
define('colSpan', 'colspan="');

/** Span tag with default CSS class */
define('span', '<span>');
/** Span tag with custom CSS class */
define('spanC', '<span class="');
/** Span end tag */
define('spanE', '</span>');

/** P tag with default CSS class */
define('p', '<p>');
/** P tag with custom CSS class */
define('pC', '<p class="');
/** P end tag */
define('pE', '</p>');

/** Space */
define('s', ' ');
/** Double-quotes, used to close attributes */
define('dq', '"');
/** Tag closure */
define('c', '>');
/** Tag closure with trailing slash */
define('sc', ' />');
/** Static (html) space */
define('nbsp', '&nbsp;');
/** Line break */
define('br', '<br />');

/** Input tag of type checkbox, to be closed with dq and c */
define('checkbox', '<input type="checkbox"');
/** Input tag of type radio, to be closed with dq and c */
define('radio', '<input type="radio"');
/** Input tag of type text, to be closed with dq and c */
define('textbox', '<input type="text"');
define('password', '<input type="password"');
/** Textarea, to be closed with dq and c */
define('textarea', '<textarea');
/** Textarea closure */
define('textareaE', '</textarea>');
/** File field to be closed */
define('filefield', '<input type="file"');
/** Input image */
define('inputimage', '<input type="image"');
/** Cols must be dq closed */
define('cols', 'cols="');
/** Rows must be dq closed */
define('rows', 'rows="');
/** Input tag of type submit, to be closed with dq and c */
define('submit', '<input type="submit"');
/** Input type reset */
define('reset', '<input type="reset"');
/** Input tag of type hidden, to be closed with dq and c */
define('hiddenField', '<input type="hidden"');
/** Input tag of type select, to be closed with dq and c */
define('combobox', '<select');
/** Select input end tag */
define('comboboxE', '</select>');
/** An option for a select input tag */
define('option', '<option>');
define('optionC', '<option');
/** Option end tag */
define('optionE', '</option>');

define('col', '<col');
define('percent', '%');
/** class attribute used in tags */
define('cl', 'class="');
/** ID attribute used in tags */
define('id', 'id="');
/** Value attribute used in tags */
define('val', ' value="');
/** Name attribute used in tags */
define('name',' name="');
/** Src attribute used in input file*/
define('src', 'src="');
/** Size attribute used in tags */
define('size',' size="');
define('width', 'width="');
/** Maxlength attribute used in tags */
define('maxLength',' maxlength="');
/** Checked attribute used in tags */
define('checked', 'checked="checked"');
/** Selected attribute used in tags */
define('selected', 'selected="selected"');
/**************************************************/

/********************** CSS **********************/
/********** Specific CSS Classes **********/
/** verticalCheckboxListing */
define('vcl', 'vcl');
/** verticalCheckboxListing query style */
define('vclQuery', 'vcl_query');
/** verticalCheckboxListing checkbox style */
define('vclCheckbox', 'vcl_checkbox');

/** verticalRadioButtonGroup */
define('vrbg', 'vrbg');
/** verticalRadioButtonGroup radio style */
define('vrbgRadio', 'vrbg_radio');

/** horizontalRadioButtonListing */
define('hrbl', 'hrbl');
define('hrbl_query', 'hrbl_query');
/** Choice width for left agreement */
define('hrblChoice', 'hrbl_choice');
/** Choice width for right agreement */
define('hrblChoice2', 'hrbl_choice2');
/** Query color for horizontalRadioButtonListing */
define('hrblQuery', 'hrbl_query');
/** color 1 for horizontalRadioButtonListing color swaping */
define('hrblColor1', 'hrbl_color1');
/** color 2 for horizontalRadioButtonListing color swaping */
define('hrblColor2', 'hrbl_color2');
define('hrbl_radio', 'hrbl_radio');


/** horizontalRadioButtonGroup */
define('hrbg', 'hrbg');

/** statementsRadioButtonListing */
define('srbl', 'srbl');
/** Query color for statementsRadioButtonListing */
define('srblQuery', 'srbl_query');
/** color 2 for statementsRadioButtonListing color swaping */
define('srblSubStatement', 'srbl_sub_statement');
/** color 1 for statementsRadioButtonListing color swaping */
define('srblColor1', 'srbl_color1');
/** color 2 for statementsRadioButtonListing color swaping */
define('srblColor2', 'srbl_color2');

/** horizontalTwoOptionsRadio */
define('htor', 'htor');

/** comboboxYearListing */
define('cyl', 'cyl');

/** comboboxStatesAndProvincesListing */
define('cspl', 'cspl');

/** textField */
define('tf', 'tf');

/** submitButton */
define('sb', 'sb');
/******************************************/

/********** General CSS Classes **********/
/** Underlined text */
define('underline', 'underline');
/** Undecorated text */
define('noUnderline', 'no_underline');

/** Bold text */
define('bold', 'bold');
/** Bolder text */
define('bolder', 'bolder');

/** Left alignment */
define('left', 'left');
/** Center alignment */
define('center', 'center');
/** Right alignment */
define('right', 'right');
/** Justified alignment */
define('justify', 'justify');

/** Wraps the text */
define('wrap', 'wrap');
/** Unwraps the text */
define('noWrap', 'nowrap');

/** 1% width */
define('minWidth', 'min_width');
/** 20% width */
define('width20', 'width20');
/** 40% width */
define('width40', 'width40');
/** 50% width */
define('width50', 'width50');
/** 60% width */
define('width60', 'width60');
/** 80% width */
define('width80', 'width80');
/** Takes all the space it can */
define('maxWidth', 'max_width');
/** Takes all the space it can */
define('filler', 'max_width');

/** Level 1 top indentation */
define('indentTop1', 'indent_top1');
/** Level 2 top indentation */
define('indentTop2', 'indent_top2');
/** Level 3 top indentation */
define('indentTop3', 'indent_top3');
/** Level 4 top indentation */
define('indentTop4', 'indent_top4');
/** Level 5 top indentation */
define('indentTop5', 'indent_top5');

/** Level 1 right indentation */
define('indentRight1', 'indent_right1');
/** Level 2 right indentation */
define('indentRight2', 'indent_right2');
/** Level 3 right indentation */
define('indentRight3', 'indent_right3');
/** Level 4 right indentation */
define('indentRight4', 'indent_right4');
/** Level 5 right indentation */
define('indentRight5', 'indent_right5');

/** Level 1 bottom indentation */
define('indentBottom1', 'indent_bottom1');
/** Level 2 bottom indentation */
define('indentBottom2', 'indent_bottom2');
/** Level 3 bottom indentation */
define('indentBottom3', 'indent_bottom3');
/** Level 4 bottom indentation */
define('indentBottom4', 'indent_bottom4');
/** Level 5 bottom indentation */
define('indentBottom5', 'indent_bottom5');

/** Level 1 left indentation */
define('indentLeft1', 'indent_left1');
/** Level 2 left indentation */
define('indentLeft2', 'indent_left2');
/** Level 3 left indentation */
define('indentLeft3', 'indent_left3');
/** Level 4 left indentation */
define('indentLeft4', 'indent_left4');
/** Level 5 left indentation */
define('indentLeft5', 'indent_left5');

/** Level 1 top margination */
define('marginTop1', 'margin_top1');
/** Level 2 top margination */
define('marginTop2', 'margin_top2');
/** Level 3 top margination */
define('marginTop3', 'margin_top3');
/** Level 4 top margination */
define('marginTop4', 'margin_top4');
/** Level 5 top margination */
define('marginTop5', 'margin_top5');

/** Level 1 right margination */
define('marginRight1', 'margin_right1');
/** Level 2 right margination */
define('marginRight2', 'margin_right2');
/** Level 3 right margination */
define('marginRight3', 'margin_right3');
/** Level 4 right margination */
define('marginRight4', 'margin_right4');
/** Level 5 right margination */
define('marginRight5', 'margin_right5');

/** Level 1 bottom margination */
define('marginBottom1', 'margin_bottom1');
/** Level 2 bottom margination */
define('marginBottom2', 'margin_bottom2');
/** Level 3 bottom margination */
define('marginBottom3', 'margin_bottom3');
/** Level 4 bottom margination */
define('marginBottom4', 'margin_bottom4');
/** Level 5 bottom margination */
define('marginBottom5', 'margin_bottom5');

/** Level 1 left margination */
define('marginLeft1', 'margin_left1');
/** Level 2 left margination */
define('marginLeft2', 'margin_left2');
/** Level 3 left margination */
define('marginLeft3', 'margin_left3');
/** Level 4 left margination */
define('marginLeft4', 'margin_left4');
/** Level 5 left margination */
define('marginLeft5', 'margin_left5');

/** Border style 1 */
define('border1', 'border1');
/** Border style 2 */
define('border2', 'border2');

/** Hides the element */
define('hidden', 'hidden');
/** Shows the element */
define('visible', 'visible');
/*****************************************/
/*************************************************/

/***************************************************** 
 **************** Class constants end ****************
 *****************************************************/

/**
 * @desc Form generator lets you creates every kind of forms datas
 * using your own CSS. It offers you multiple ways of
 * positionning your forms and lessen the burden of the
 * HTML writing You simply provide the questions to
 * the function of your choice so it will display
 * your form the way you want.
 * 
 * Standard W3C XHTML STRICT 1.0  no errors !
 * W3C CSS no errors !
 * 
 * @Author Steven Rosato and Marco Roy
 * @version 1.13 Feb 2008
 * 
 * Fix log
 * 
 * 1.13 Finished documentation, first stable version
 */
	
class FormGenerator
{
	/**
	 * Every states from the US sorted alphabetically in an array
	 */
	public $_states = array(
		'Alabama',
		'Alaska',
		'Arizona',
		'Arkansas',
		'California',
		'Colorado',
		'Connecticut',
		'Delaware',
		'District of Columbia',
		'Florida',
		'Georgia',
		'Hawaii',
		'Idaho',
		'Illinois',
		'Indiana',
		'Iowa',
		'Kansas',
		'Kentucky',
		'Louisiana',
		'Maine',
		'Maryland',
		'Massachusetts',
		'Michigan',
		'Minnesota',
		'Mississippi',
		'Missouri',
		'Montana',
		'Nebraska',
		'Nevada',
		'New Hampshire',
		'New Jersey',
		'New Mexico',
		'New York',
		'North Carolina',
		'North Dakota',
		'Ohio',
		'Oklahoma',
		'Oregon',
		'Pennsylvania',
		'Rhode Island',
		'South Carolina',
		'South Dakota',
		'Tennessee',
		'Texas',
		'Utah',
		'Vermont',
		'Virginia',
		'Washington',
		'West Virginia',
		'Wisconsin',
		'Wyoming'
	);
	
	/**
	 * All provinces from Canada sorted alphabetically
	 * in an array
	 */
	public $_provinces = array(
		'Alberta',
		'British Columbia',
		'Manitoba',
		'New Brunswick',
		'Newfoundland',
		'Northwest Territories',
		'Nunavut',
		'Nova Scotia',
		'Ontario',
		'Prince Edward Island',
		'Quebec',
		'Saskatchewan',
		'Yukon'
	);
	
	/**
	 * Determines if the class applies validation
	 * according to the validation.class.php
	 * that must be linked with when this constuctor
	 * is called
	 */
	public $_validation = false;
	
	/**
	 * Validation class object. Shouldn't be used outside
	 * of class but still possible in some cases.
	 */
	public $_err = null;
	
	/**
	 * @desc The constructor
	 *
	 * @param Boolean $validation If the class will be linked with validation
	 * @param Validation &$class The validation object (use validation.class.php)
	 * 
	 * @return FormGenerator
	 * 
	 * @author	Steven Rosato
	 */
	public function FormGenerator($validation = false, Validation &$class = null)
	{
		if($validation){
			$this->_validation = $validation;
			$this->_err = &$class;
		}
	}
	
	/**
	 * @desc If we have validation linked we return the error if it isn't empty
	 *
	 * @param string $error
	 * 
	 * @return The error as a string or false if validation isn't actived or the error is empty
	 * 
	 * @author Steven Rosato
	 */
	private function validateError($error)
	{
		if($this->_validation) {
			if($this->_err->getError($error) != "") {
				return s.$this->_err->getError($error);
			}
		}
		return false;
	}
	
	/**
	 * If we have validation linked we return the post value
	 * if it isn't empty
	 *
	 * @param string $value
	 * 
	 * @return The post value as a string or false if validation isn't actived or the value is empty
	 * 
	 * @author Steven Rosato
	 */
	private function validateValue($value)
	{
		if($this->_validation) {
			$val = $this->_err->getValue($value);
			if( $val != "" ){ //not using empty here because we can get "0"
				return $val;
			}
		}
		return false;
	}
	
	/**
	 * @desc This function creates a vertical alignment with queries and checkboxes using a table
	 * You have the possibility to align them the way you want using the QUERY_xx constants
	 *
	 * @param string $id The id you want to apply to your checkboxes (q1,q2,...)
	 * @param Array $questions Array containing all the questions in String
	 * @param Integer $display Display position (use constants QUERY_xx)
	 * @param Array $values (optional) Array containing all the values to be applied to the checkboxes
	 * 
	 * @return String Table based html output.
	 * 
	 * @author Steven Rosato
	 */
	public function verticalCheckboxListing($id, $questions, $display, $values = array(), $separator = '_')
	{
		$html = "";
		
		if($error = $this->validateError($id)) {
				$html .= p.$error.pE;
		}
		
		$html .= tableC.vcl.dq.c;
			
		for( $i = 0; $i < count($questions); $i++ )
		{
			$html .= trC.vcl.dq.c.tdC;
			$val = !empty($values) ? $values[$i] : $i;
			switch($display)
			{
				case QUERY_LL:
					$html .= vclQuery.s.left.s.indentLeft1.dq.c.$questions[$i].tdE;
					$html .= tdC.vclCheckbox.s.left.dq.c.checkbox.s.id.$id.$separator.$i.dq.name.$id.$separator.$i.dq.val.$val.dq;
					$html .= $this->validateValue($id.$separator.$i) === (String)$val ? checked.s : "";
					$html .= sc.tdE;
					break;
				case QUERY_LR:
					$html .= vclQuery.s.right.s.indentLeft1.dq.c.$questions[$i].tdE;
					$html .= tdC.vclCheckbox.s.right.dq.c.checkbox.s.id.$id.$separator.$i.dq.name.$id.$separator.$i.dq.val.$val.dq;
					$html .= $this->validateValue($id.$separator.$i) === (String)$val ? checked.s : "";
					$html .= sc.tdE;
					break;
				case QUERY_RL:
					$html .= vclCheckbox.s.left.dq.c.checkbox.s.id.$id.$separator.$i.dq.name.$id.$separator.$i.dq.val.dq;
					$html .= $this->validateValue($id.$separator.$i) === (String)$val ? checked.s : "";
					$html .= sc.tdE;
					$html .= tdC.vclQuery.dq.c.$questions[$i].tdE;
					break;
				case QUERY_RR:
					$html .= vclCheckbox.s.left.dq.c.checkbox.s.id.$id.$separator.$i.dq.name.$id.$separator.$i.dq.val.dq;
					$html .= $this->validateValue($id.$separator.$i) === (String)$val ? checked.s : "";
					$html .= sc.tdE;
					$html .= tdC.vclQuery.s.right.dq.c.$questions[$i].tdE;
					break;
			}
			$html .= trE;
		}
		$html .= tableE;
		return $html;
	}
	
	/**
	 * @desc This function creates a vertical alignment with queries and radiobuttons in a group using a table
	 * You have the possibility to align them the way you want using the QUERY_xx constants
	 *
	 * @param string $id The id you want to apply to your radioButtons (q1,q2,...)
	 * @param Array $questions Array containing all the questions in String
	 * @param Integer $display Display position (use constants QUERY_xx)
	 * @param Array $values (optional) Array containing all the values to be applied to the radiobuttons
	 * 
	 * @return String Table based html output.
	 * 
	 * @author Steven Rosato
	 */
	public function verticalRadioButtonGroup($id, $questions, $display, $values = array(), $separator = '_')
	{
		$html = "";
		
		if($error = $this->validateError($id)) {
			$html .= p.$error.pE;
		}
		
		$html .= table;
		for($i = 0;$i<count($questions);$i++)
		{
			$html .= trC.vrbg.dq.c.tdC;
			$val = count($values) > 0 ? $values[$i] : $i;
			switch($display)
			{
				case QUERY_LL:
					$html .= vrbg.s.left.dq.c.$questions[$i].tdE;
					$html .= tdC.vrbgRadio.s.left.dq.c.radio.s.id.$id.$separator.$i.dq.name.$id.dq.val.$val.dq;
					$html .= $this->validateValue($id) === (String)$val ? checked.s : "";
					$html .= sc.tdE;
					break;
				case QUERY_LR:
					$html .= vrbg.s.right.dq.c.$questions[$i].tdE;
					$html .= tdC.vrbgRadio.s.right.s.minWidth.dq.c.radio.s.id.$id.$separator.$i.dq.name.$id.dq.val.$val.dq;
					$html .= $this->validateValue($id) === (String)$val ? checked.s : "";
					$html .= sc.tdE;
					break;
				case QUERY_RL:
					$html .= vrbgRadio.s.left.dq.c.radio.s.id.$id.$separator.$i.dq.name.$id.dq.val.$val.dq;
					$html .= $this->validateValue($id) === (String)$val ? checked.s : "";
					$html .= sc.tdE;
					$html .= tdC.vrbg.dq.c.$questions[$i].tdE;
					break;
				case QUERY_RR:
					$html .= vrbgRadio.s.left.dq.c.radio.s.id.$id.$separator.$i.dq.name.$id.dq.val.$val.dq;
					$html .= $this->validateValue($id) === (String)$val ? checked.s : "";
					$html.= sc.tdE;
					$html .= tdC.vrbg.s.right.dq.c.$questions[$i].tdE;
					break;
			}
			$html .= trE;
		}
		$html .= tableE;
		return $html;
	}
	
	/**
	 * @desc This function creates an horizontal alignment listing with radiobuttons using a group.
	 * It consists in having a series of questions to output using an agreement meter.
	 *
	 * E.G Having 7 radio buttons aligned from I completely agree to completely disagree
	 * 
	 * If you use this function only for one question consider passing a separator for the groupID
	 * since the index will append either 0 or what was passed in the values array
	 *
	 * @param string $groupID The group id. Use a separator when only requesting one question
	 * @param array $questions Array of questions, pass null if no questions, but use separator for groupID
	 * @param int $nbChoices The number of radio buttons to horizontally align
	 * @param mixed $agree The left most text in the alignment. Doesn't necessarily says the text must be an agreement. Can be an array containing another array as well (in case aggreements change from questions to questions).
	 * @param mixed $disagree The right most text in the alignment. Doesn't necessarily says the text must be a disagreement. Can be an array containing another array as well (in case disaggreements change from questions to questions).
	 * @param array $values (optional) The values to append to each radiobuttons. If no array is passed, will use the for index beggining at 0.
	 * @param array $reversedQueries (optional) To reverse the values
	 * 
	 * @return Table based HTML output
	 * 
	 * @author Steven Rosato
	 */
	public function horizontalRadioButtonListing($groupID, $questions, $nbChoices, $agree, $disagree, $values = array(), $reversedQueries = null)
	{
		if($questions == null) {
			$questions = array(""); //uses too much memory? change it then..
		}
		
		if($values == null){
			$values = array();
		}
		
		if($reversedQueries != null){
		$tempArray = array();
			for ($i = 0 ; $i < count($reversedQueries) ; $i++) {
				$tempArray[$reversedQueries[$i]] = true;
			}
			for ($i = 0 ; $i < count($questions) ; $i++) {
				if(!isset($tempArray[$i])){
					$tempArray[$i] = false;
				}
			}
			$reversedQueries = $tempArray;
		}
		
		$html = "";
		
		for( $i = 0; $i < count($questions); $i++ )
		{
			/* if we already have a separator we don't add $i */
			$arr = explode('_', $groupID);
			if(count($arr) > 1) {
				if(strlen($arr[count($arr) - 1]) == 0){
					$str = $groupID.$i;
				} else {
					$str = $groupID;
					
				}
			} else {
				$str = $groupID.$i;
			}
			
			$first = is_array($agree) ? $agree[$i] : $agree;
			$end = is_array($disagree) ? $disagree[$i] : $disagree;
			
			//$html = tableC.hrbl.dq.c;
			
			//$html .= $questions[$i] != "" ? trC.hrbl.dq.c.tdC.hrbl_query.s.noWrap.dq.colSpan.count($values).dq.c.$questions[$i].tdE.trE : "";
			$html .= div.$questions[$i].divE;
			$html .= br;
			if(is_array($first)){
				for ($j = 0 ; $j < count($first) ; $j++) {
					$html .= ($error = $this->validateError($str.'_'.$j)) ? div.$error.divE : "";
					$html .= tableC.hrbl.dq.c;
					$html .= tr.tdC.hrblChoice.dq.c.$first[$j].tdE;
					for( $k = 0; $k < $nbChoices; $k++ )
					{
						$val = '';
						if($reversedQueries != null){
							if($reversedQueries[$i] && count($values) > 0){
								$val = $values[(count($values) - $k + 1)];
							} else {
								$val = count($values) > 0 ? $values[$j] : $j;
							}
						} else {
							$val = count($values) > 0 ? $values[$k] : $k;
						}
						$html .= tdC.hrbl.s.hrbl_radio.dq.c.radio.name.$str.'_'.$j.dq.val.$val.dq;
						$html .= $this->validateValue($str.'_'.$j) === (String)$val ? checked.s : "";
						$html .= sc.tdE;
					}
					$html .= tdC.hrblChoice2.dq.c.$end[$j].tdE.trE.tr.td.nbsp.tdE.trE;
					$html .= tableE;
				}
			} else {
				$html .= ($error = $this->validateError($str)) ? div.$error.divE : "";
				$html .= tableC.hrbl.dq.c;
				$html .= tr.tdC.hrblChoice.dq.c.$first.tdE;
				for( $j = 0; $j < $nbChoices; $j++ )
				{
					$val = '';
					if($reversedQueries != null){
						if($reversedQueries[$i] && count($values) > 0){
							$val = $values[(count($values) - ($j + 1))];
						} else {
							$val = count($values) > 0 ? $values[$j] : $j;
						}
					} else {
						$val = count($values) > 0 ? $values[$j] : $j;
					}
					$html .= tdC.hrbl.s.hrbl_radio.dq.c.radio.name.$str.dq.val.$val.dq;
					$html .= $this->validateValue($str) === (String)$val ? checked.s : "";
					$html .= sc.tdE;
				}
				$html .= tdC.hrblChoice2.dq.c.$end.tdE.trE.tr.td.nbsp.tdE.trE;
				$html.= tableE;
			}
		}
		
		return $html;
	}
	
	/**
	 * @desc This function creates an horizontal alignment grouping with radiobuttons using a group.
	 * 
	 * It consists in having a series of questions to output using an agreement meter each tagged with its own text
	 *
	 * E.G Having 3 radio buttons : 
	 * Favorite color :
	 * blue o green o red o
	 * 
	 * If you use this function only for one question consider passing a separator for the groupID
	 * since the index will append either 0 or what was passed in the values array
	 *
	 * @param string $groupID The group id.
	 * @param array $questions Array of questions, pass null if no questions.
	 * @param array choices The choices to horizontally align beside the radiobutton.
	 * @param array $values (optional) The values to append to each radiobuttons. If no array is passed, will use the for index beggining at 0.
	 * @param string $separator (optional) Separator to use in values.
	 * @param int $position (optionnal) QUERY_UD or QUERY_DU will put the choices below or under the radio buttons (when below, use a <br /> to make it appear on top of the radio button)
	 * 
	 * @return Table based HTML output
	 * 
	 * @author Steven Rosato
	 */
	public function horizontalRadioButtonGroup($groupID, $questions, $choices, $values = array(), $separator = '_', $position = QUERY_UD)
	{
		
		if($values == null) { $values = array(); }
		if($separator == null) { $separator = '_'; }
		
		$html = table;
		
		if($position == QUERY_DU){
			for($j=0;$j<count($choices);$j++)
			{
				$html .= col.s.width.(100/count($choices)).percent.dq.sc;
			}
		}

		for($i=0;$i<count($questions);$i++)
		{
			$html .= trC.hrbg.s.left.dq.c.tdC.hrbg.dq.s.colSpan.count($choices).dq.c.$questions[$i];
			$html .= ($error = $this->validateError($groupID.$separator.$i)) ? br.$error : "";
			$html .= tdE.trE;
			
			
			switch($position){
				case QUERY_UD:
					$html .= tr;
					for($j=0;$j<count($choices);$j++)
					{
						$val = count($values) > 0 ? (string)$values[$j]: $j;
						$html .= tdC.hrbg.s.left.s.indentLeft1.s.indentBottom2.dq.c.$choices[$j].nbsp.nbsp.radio.name.$groupID.$separator.$i.dq.val.$val.dq;
						$html .= $this->validateValue($groupID.$separator.$i) === (String)$val ? checked.s : "";
						$html .= sc.tdE;
					}
					$html .= trE;
					break;
				case QUERY_DU:
					
					$html .= tr.td.nbsp.tdE.trE.tr;
					
					for($j=0;$j<count($choices);$j++)
					{
						$val = count($values) > 0 ? (string)$values[$j]: $j;
						$html .= tdC.hrbg.s.center.s.indentLeft1.s.indentBottom2.dq.c.radio.name.$groupID.$separator.$i.dq.val.$val.dq.s;
						$html .= $this->validateValue($groupID.$separator.$i) === (String)$val ? checked.s : "";
						$html .= sc.tdE;
					}
					$html .= trE;
					
					$html .= tr;
					for($j=0;$j<count($choices);$j++)
					{
						$html .= tdC.hrbg.s.center.s.indentLeft1.s.indentBottom2.dq.c.$choices[$j].tdE;
					}
					$html .= trE;
					
					break;
			}
		}
		
		$html .= tableE;
		
		return $html;
	}
	
	/**
	 * @desc This function creates a statement listing using radio button each embended in their own group.
	 * Questions are to the left while radiobuttons are horizontally placed besides the question
	 * Using swap color makes it look a lot better
	 * At every 10 questions, it reprints the header.
	 * 
	 * This is the most popular function of this class.
	 *
	 * @param string $groupID
	 * @param array $questions Array containing all questions in strings
	 * @param string $title Title to use in the top left
	 * @param array $agreements Array containing all radiobuttons' choices in strings
	 * @param boolean $swapColor true to append color and swapping of it
	 * @param array $values (optionnal) Values to be applied to the radiobuttons. Index will be used if not provided
	 * @param string $separator (optionnal) The separator to append in radiobuttons names
	 * @param array $subStatement (optionnal) The index and value of sub-statement labels (ex: $subStatement['7'] = 'foobar')
	 * 
	 * @return Table based HTML output
	 * 
	 * @author Steven Rosato
	 */
	public function statementsRadioButtonListing($groupID, $questions, $title, $agreements, $swapColor, $values = array(), $separator = '_', $subStatement = array())
	{
		if( $values == null || $values == 'default' ) {
			$values = array();
		}
		if( $separator == null || $separator == 'default' ) {
			$separator = '_';
		}
		if( $subStatement == null || $subStatement == 'default' ) {
			$subStatement = array();
		}
		
		$groupID = rtrim($groupID, $separator); //trim if we already have same separator in groupID
		
		$printAgreements = 0; //print the agreements for every 2 sub-statements
		
		$html = tableC.srbl.dq.c;
		
		for( $i = 0; $i < count($questions); $i++ )
		{
			if( isset($subStatement[$i]) ) {
				if( ($printAgreements++ % 2) == 0 ) {
					$html .= trC.filler.dq.c.tdC.srbl.s.srblQuery.s.center.dq.c.$title.tdE;
					
					for( $j = 0; $j < count($agreements); $j++ )
					{
						$html .= tdC.srbl.s.srblQuery.s.center.dq.c.$agreements[$j].tdE;
					}
					
					$html .= trE;
				}
				$html .= trC.srbl.s.srblSubStatement.s.bold.s.left.dq.c.tdC.srbl.dq.colSpan.(count($agreements) + 1).dq.c.$subStatement[$i].tdE.trE;
			} elseif( count($subStatement) < 1 ) {
				if( ($i % 10) == 0 && (count($questions) - $i) > 2 ) {
					$html .= trC . filler . dq . c . tdC . srbl . s . srblQuery . s . center . dq . c . $title . tdE;
					
					for( $j = 0; $j < count($agreements); $j ++ ) {
						$html .= tdC . srbl . s . srblQuery . s . center . dq . c . $agreements [$j] . tdE;
					}
					
					$html .= trE;
				}
			}
			
			if( $swapColor && $i % 2 == 0 ) {
				$html .= trC.srbl.s.srblColor1.s.left.dq.c.tdC.srbl.dq.c.$questions[$i];
				$html .= ($error = $this->validateError($groupID.$separator.$i)) ? br.$error : "";
				$html .= tdE;
			} elseif ( $swapColor && $i % 2 == 1 ) {
				$html .= trC.srbl.s.srblColor2.s.left.dq.c.tdC.srbl.dq.c.$questions[$i];
				$html .= ($error = $this->validateError($groupID.$separator.$i)) ? br.$error : "";
				$html .= tdE;
			} else {
				$html .= trC.srbl.dq.c.tdC.srbl.dq.c.$questions[$i];
				$html .= ($error = $this->validateError($groupID.$separator.$i)) ? br.$error : "";
				$html .= tdE;
			}
			
			for( $j = 0; $j < count($agreements); $j++ )
			{
				$val = count($values) > 0 ? $values[$j] : $j;
				$html .= tdC.srbl.s.center.s.minWidth.dq.c.radio.name.$groupID.$separator.$i.dq.val.$val.dq;
				$html .= $this->validateValue($groupID.$separator.$i) === (String)$val ? checked.s : "";
				$html .= sc.tdE;
			}
			
			$html .= trE;
		}
		
		$html .= tableE;
		
		return $html;
	}
	
	/**
	 * @desc This function creates a vertical statement listing using radio button. The radio button
	 * group is vertical instead of horizontal like the standard statement listing.
	 * Questions are to the left while radiobuttons are vertically placed besides the question
	 * Using swap color makes it look a lot better
	 * At every 10 questions, it reprints the header.
	 *
	 * @param string $groupID
	 * @param array $questions Array containing all questions in strings
	 * @param string $title Title to use in the top left
	 * @param array $agreements Array containing all radiobuttons' choices in strings
	 * @param boolean $swapColor true to append color and swapping of it
	 * @param array $values (optionnal) Values to be applied to the radiobuttons. Index will be used if not provided
	 * @param string $separator (optionnal) The separator to append in radiobuttons names
	 * @param array $subStatement (optionnal) The index and value of sub-statement labels (ex: $subStatement['7'] = 'foobar')
	 * 
	 * @return Table based HTML output
	 * 
	 * @author Steven Rosato, Marco Roy
	 */
	public function verticalStatementsRadioButtonListing($groupID, $questions, $title, $agreements, $swapColor, $values = array(), $separator = '_', $subStatement = array())
	{
		if( $values == null || $values == 'default' ) {
			$values = array();
		}
		if( $separator == null || $separator == 'default' ) {
			$separator = '_';
		}
		if( $subStatement == null || $subStatement == 'default' ) {
			$subStatement = array();
		}
		
		$groupID = rtrim($groupID, $separator); //trim if we already have same separator in groupID
		
		$printAgreements = 0; //print the agreements for every 2 sub-statements
		
		$html = tableC.srbl.dq.c;
		
		$specialCount = 0;
		
		for( $i = 0; $i < count($questions); $i++ )
		{
			$special = false;
			
			if( strpos($questions[$i], '[fg_text]') == 1 ) {
				$questions[$i] = substr( $questions[$i], 10 );
				$special = true;
			}
			
			if( isset($subStatement[$i]) ) {
				if( ($printAgreements++ % 2) == 0 ) {
					$html .= trC.filler.dq.c.tdC.srbl.s.srblQuery.s.center.dq.c.$title.tdE;
					
					for( $j = 0; $j < count($agreements); $j++ )
					{
						$html .= tdC.srbl.s.srblQuery.s.center.dq.c.$agreements[$j].tdE;
					}
					
					$html .= trE;
				}
				$html .= trC.srbl.s.srblSubStatement.s.bold.s.left.dq.c.tdC.srbl.dq.colSpan.(count($agreements) + 1).dq.c.$subStatement[$i].tdE.trE;
			} elseif( count($subStatement) < 1 ) {
				if( ($i % 10) == 0 && (count($questions) - $i) > 2 ) {
					$html .= trC . filler . dq . c . tdC . srbl . s . srblQuery . s . center . dq . c . $title . tdE;
					
					for( $j = 0; $j < count($agreements); $j ++ ) {
						$html .= tdC . srbl . s . srblQuery . s . center . dq . c . $agreements [$j];
						$html .= ($error = $this->validateError($groupID.$separator.$j)) ? br.$error : "";
						$html .= tdE;
					}
					
					$html .= trE;
				}
			}
			
			if( $swapColor && $i % 2 == 0 ) {
				$html .= trC.srbl.s.srblColor1.s.left.dq.c.tdC.srbl.dq.c.$questions[$i].tdE;
			} elseif ( $swapColor && $i % 2 == 1 ) {
				$html .= trC.srbl.s.srblColor2.s.left.dq.c.tdC.srbl.dq.c.$questions[$i].tdE;
			} else {
				$html .= trC.srbl.dq.c.tdC.srbl.dq.c.$questions[$i].tdE;
			}
			
			if( $special ) {
				for( $j = 0; $j < count($agreements); $j++ )
				{
					$val = count($values) > 0 ? $values[$i] : $i;
					$html .= tdC.srbl.s.center.s.minWidth.dq.c;
					$html .= $this->textField($groupID.$separator.'s'.$specialCount, $groupID.$separator.'s'.$specialCount, '', 20, 20);
					$html .= tdE;
					$specialCount++;
				}
			} else {
				for( $j = 0; $j < count($agreements); $j++ )
				{
					$val = count($values) > 0 ? $values[$i] : $i;
					$html .= tdC.srbl.s.center.s.minWidth.dq.c.radio.name.$groupID.$separator.$j.dq.val.$val.dq;
					$html .= $this->validateValue($groupID.$separator.$j) === (String)$val ? checked.s : "";
					$html .= sc.tdE;
				}
			}
			
			$html .= trE;
		}
		
		$html .= tableE;
		
		return $html;
	}
	
	/**
	 * @desc The function creates two options horizontally aligned using radiobuttons in a group.
	 *
	 * @param string $groupID The group id.
	 * @param string $option1 Text besides the first radiobutton
	 * @param string $option2 Text besides the second radiobutton
	 * @param string $value1 The value of the first radiobutton
	 * @param string $value2 The value of the second radiobutton
	 * 
	 * @return Table based HTML output
	 * 
	 * @author Steven Rosato
	 */
	public function horizontalTwoOptionsRadio($groupID, $option1, $option2, $value1, $value2)
	{
		$html = table;
		
		$html .= ($error = $this->validateError($groupID)) ? tr.td.$error.tdE.trE : "";
		$html .= tr.td.$option1.radio.name.$groupID.dq.val.$value1.dq;
		$html .= $this->validateValue($groupID) === (String)$value1 ? checked.s : "";
		$html .= sc.tdE;
		$html .= td.$option2.radio.name.$groupID.dq.val.$value2.dq;
		$html .= $this->validateValue($groupID) === (String)$value2 ? checked.s : "";
		$html .= sc.tdE.trE;
		
		$html .= tableE;
		
		return $html;
	}
	
	/**
	 * @desc This function creates a combobox with years
	 *
	 * @param string $name Name of the combobox
	 * @param int $startYear First year in combobox
	 * @param boolean $desc if true, Descendant, otherwise ascendent
	 * @param int $endYear Last year in combobox
	 * 
	 * @return HTML output
	 * 
	 * @author Steven Rosato
	 */
	public function comboboxYearListing($name, $startYear, $desc, $endYear = null)
	{
		//TODO: Link validation
		
		$html = combobox.name.$name.dq.c;
		
		if ($endYear == null) {
			$endYear = date('Y');
		}
		
		if ($desc) {
			for($i = $startYear;$i <= $endYear;$i++){
				$html .= option.$i.optionE;
			}
		} else {
			for($i = $endYear;$i >= $startYear; $i--){
				$html .= option.$i.optionE;
			}
		}
		
		$html .= comboboxE;
		
		return $html;
	}
	
	/**
	 * @desc The function creates a combobox with US and Canada states and provinces
	 * 
	 * One of the two boolean parameters MUST be true.
	 *
	 * @param string $name The name of the combobox
	 * @param boolean $states True appends the US states
	 * @param boolean $provinces True append the Canada provinces
	 * 
	 * @return HTML output
	 * 
	 * @author Steven Rosato
	 */
	public function comboboxStatesAndProvincesListing($name, $states, $provinces)
	{	
		//TODO: Link validation
		
		$html = combobox.name.$name.dq.c;
		$html .= option.'--CANADA--'.optionE;
		
		if($provinces){		
			for($i=0;$i<count($this->_provinces);$i++){
				$html .= option.$this->_provinces[$i].optionE;;
			}
		}
		
		$html .= option.'--United States--'.optionE;
		
		if ($states) {
			for($i=0;$i<count($this->_states);$i++){
				$html .= option.$this->_states[$i].optionE;;
			}
		}
		
		$html .= comboboxE;
		
		return $html;
	}
	
	/**
	 * @desc This function creates a textField
	 *
	 * @param string $id Textfield's ID
	 * @param string $name Textfield's name
	 * @param string $value Textfield's value
	 * @param string $size Textfield's size
	 * @param string $maxlength Textfield's maxlength
	 * 
	 * @return HTML output
	 * 
	 * @author Steven Rosato 
	 */
	public function textField($id, $name, $value, $size, $maxlength)
	{
		$html = span.textbox.s.cl.tf.dq.s.id.$id.dq.s.name.$name.dq.s.val;
		$html .= ($val = $this->validateValue($id)) ? $val : $value;
		$html .= dq.size.$size.dq.maxLength.$maxlength.dq.sc.spanE;
		$html .= ($error = $this->validateError($id)) ? $error : "";
		
		return $html;
	}
	
	public function passwordField($id, $name, $value, $size, $maxlength)
	{
		$html = span.password.s.cl.tf.dq.s.id.$id.dq.s.name.$name.dq.s.val;
		$html .= ($val = $this->validateValue($id)) ? $val : $value;
		$html .= dq.size.$size.dq.maxLength.$maxlength.dq.sc.spanE;
		$html .= ($error = $this->validateError($id)) ? $error : "";
		
		return $html;
	}
	
	/**
	 * @desc This functions creates a submit button and an hidden field (optionnal)
	 *
	 * @param string $id Submit button's ID
	 * @param string $name Submit button's name
	 * @param string $value Submit button's value
	 * @param boolean $hiddenField true to add an hiddenfield with same value and name
	 * @param string $hiddenID (optional) Hidden field's ID
	 * 
	 * @return HTML output
	 * 
	 * @author Steven Rosato
	 */
	public function submitButton($id, $name, $value, $hiddenField, $hiddenID = "hidden")
	{
		$html = submit.s.id.$id.dq.name.$name.dq.val.$value.dq.sc;
		
		if($hiddenField) {
			$html .= hiddenField.s.id.$hiddenID.dq.name.$name.dq.val.$value.dq.sc;
		}
		
		return $html;
	}
	
	/**
	 * Creates a reset button
	 *
	 * @param String $value Text to display on the button
	 * @param String $name The input name
	 * @return HTML output
	 */
	public function resetButton($value, $name = 'reset')
	{
		$html = reset.s.name.$name.dq.s.val.$value.dq.s.sc;
		
		return $html;
	}
	
	/**
	 * Creates an hiddenField
	 *
	 * @return HTML output
	 * 
	 * @author Steven Rosato
	 */
	public function hiddenField($id, $name, $value)
	{
		return hiddenField.s.id.$id.dq.name.$name.dq.val.$value.dq.sc;
	}
	
	/**
	 * Creates a combobox / a list
	 *
	 * @param String $id The input unique id
	 * @param String $name The input name
	 * @param Array $data All the datas to be listed 
	 * @param String $select (optionnal) The first item.
	 * @return HTML output
	 * 
	 * @author Steven Rosato
	 */
	public function comboBox($id, $name, $data, $select = '--Select--')
	{
		$html = combobox.s.id.$id.dq.s.name.$name.dq.c;
		$html .= option.$select.optionE;
		for ($i = 0 ; $i < count($data) ; $i++) {
			$html .= $this->validateValue($id) == $data[$i] ? optionC.s.selected.c : option;
			$html .= $data[$i].optionE;
		}
		$html .= comboboxE;
		
		$html .= ($error = $this->validateError($id)) ? $error : "";
		
		return $html;
	}
	
	/**
	 * Creates a combobox / a list
	 *
	 * @param String $id The input unique id
	 * @param String $name The input name
	 * @param Array $data All the datas to be listed 
	 * @param mixed $selected (optionnal) The selected item (Either index or option string)
	 * @return HTML output
	 * 
	 * @author Steven Rosato
	 */
	public function comboBoxSelected($id, $name, $data, $selected = 0, $values = null )
	{
		$html = combobox.s.id.$id.dq.name.$name.dq.c;
		for ($i = 0 ; $i < count($data) ; $i++) {
			$html .= optionC;
			if( $this->validateValue($id) == $data[$i] || $i == $selected || $data[$i] === $selected ) {
				$html .= s.selected;
			}
			
			if( $values != null ) {
				$html .= val.$values[$i].dq;
			}
			
			$html .= c.$data[$i].optionE;
		}
		$html .= comboboxE;
		
		$html .= ($error = $this->validateError($id)) ? $error : "";
		
		return $html;
	}
	
	/**
	 * Creates a textArea. If you include maxLength.js in the <head>
	 * and setMaxLength() in the <body> onload event, a javascript
	 * will be autogenerated with a maxlength of cols * rows.
	 * Validation should be applied for maxlength though in 
	 * case someone copy paste from another document. That is why
	 * text locking wasn't implemented. (validation.class.php recommended ;) )
	 *
	 * @param String $name The textarea name (id)
	 * @param Integer $cols Number of columns
	 * @param Integer $row Number of rows
	 * @return HTML output
	 * 
	 * @author Steven Rosato
	 */
	public function textArea($name, $cols, $row, $value = '')
	{
		$html = ($error = $this->validateError($name)) ? $error.br : "";
		$html .= textarea.name.$name.dq.s.cols.$cols.dq.s.rows.$row.dq.c;
		if(empty($value)) {
			$html .= $this->validateValue($name);
		} else {
			$html .= $value;
		}
		
		$html .= textareaE;
		
		return $html;
	}
	
	/**
	 * Creates a file field.
	 * 
	 * Encoding type of a form must be
	 * enctype="multipart/form-data"
	 *
	 * @param String $name The name of the field
	 * @param Integer $filesize (optionnal) Max file size in ko
	 * @return HTML Output
	 * 
	 * @author Steven Rosato
	 */
	public function fileBrowser($name , $filesize = 1024) //1MB
	{
		$html = ($error = $this->validateError($name)) ? $error.br : "";
		$html .= filefield.s.name.$name.dq.s.val.$filesize.dq.sc;
		
		return $html;
	}
	
	/**
	 * Creates an input of type image
	 *
	 * @param String $name Name of the input
	 * @param String $src File path (relative)
	 * 
	 * @return HTML output
	 * 
	 * @author Steven Rosato
	 */
	public function inputImage($name, $src)
	{
		$html = inputimage.s.name.$name.dq.s.src.$src.dq.s.sc;
		
		return $html;
	}
	
	/**
	 * Creates a single checkbox.
	 * 
	 * //TODO: tester cette méthode
	 *
	 * @param String $name The name of the checkbox
	 * @param boolean $selected Selected at first, or not
	 * @return HTML output
	 * 
	 * @author Steven Rosato
	 */
	public function checkbox($name, $checked = false)
	{
		$html = checkbox.s.name.$name.dq.s;
		
		if( !$checked ) {
			$html .= $this->validateValue($name);
		} else {
			$html .= checked;
		}
		$html .= sc;
		
		return $html;
	}
	
	/**
	 * Creates a single radiobutton
	 *
	 * @param String $name The name of the radiobutton
	 * @param String $value The value
	 * @param boolean $selected Selected at first, or not
	 * @return HTML output
	 */
	public function radiobutton($name, $value, $selected = false)
	{
		$html = radio.s.name.$name.dq.s.val.$value.dq.s;
		
		if( !$selected ) {
			$html .= $this->validateValue($name) === $value ? checked : '';
		} else {
			$html .= checked;
		}
		
		$html .= sc;
		
		return $html;
	}
}
?>