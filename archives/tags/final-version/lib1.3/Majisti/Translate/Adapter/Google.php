<?php
/**
 * Google translate adapter
 *
 * @author Rune Kaagaard <rune <at> prescriba.com>
 */

/** Zend_Locale */
require_once 'Zend/Locale.php';

/** Zend_Translate_Adapter */
require_once 'Zend/Translate/Adapter.php';

/** Zend_Http_Client */
require_once 'Zend/Http/Client.php';

/**
 * @category   Zend
 * @package    Zend_Translate
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Majisti_Translate_Adapter_Google extends Zend_Translate_Adapter 
{
	/**
	 * Generates the adapter
	 *
	 * @param  array               $data     Translation data
	 * @param  string|Zend_Locale  $locale   OPTIONAL Locale/Language to set, identical with locale identifier,
	 *                                       see Zend_Locale for more information
	 * @param  array               $options  OPTIONAL Options to set
	 */
	public function __construct($data, $locale = null, array $options = array()) 
	{
		parent::__construct($data, $locale, $options);
		//autoadd translation
		$this->addTranslation ('', $locale);

	}

	/**
	 * Overloads translate method
	 *
	 * The current translate method only support return of already preloaded translations,
	 * so overloading translate method and getting translations from google instead.
	 *
	 * @param string $messageId
	 * @param string $locale
	 * @return string
	 */
	public function translate ($messageId, $locale = null) 
	{
		//get locale
		$locale = $this->_options['locale'];

		//get which language to translate from, default to english
		if (!empty($this->_options['from'])) {
			$fromLanguage = $this->_options['from'];
		} else {
			$fromLanguage = 'en';
		}

		//initialize http client
		$Client = new Zend_Http_Client("http://ajax.googleapis.com/ajax/services/language/translate");
		$Client->setParameterGet (array(
			'v'=>'1.0',
			'q'=>$messageId,
			'langpair'=>"$fromLanguage|$locale"
		));

		//make request to google api
		try {
			//get and json decode response
			$Response = $Client->request();
			$ResponseJson = json_decode ($Response->getBody());
		} catch (Zend_Translate_Exception $e) {
			if ($this->_options['debug']) {
				require_once 'Zend/Translate/Exception.php';
				throw new Zend_Translate_Exception("Could cot connect to google translate service");
			} else {
				//if not debugging return original string
				return $messageId;
			}
		}

		//validate return
		if (!empty ($ResponseJson->responseStatus) && $ResponseJson->responseStatus === 200) {
			//correct response, returning translated string
			return ($ResponseJson->responseData->translatedText);
		} else {
			if ($this->_options['debug']===true) {
				require_once 'Zend/Translate/Exception.php';
				if (!empty($ResponseJson->responseStatus) && !empty($ResponseJson->responseStatus)) {
					$msg = $ResponseJson->responseDetails;
					$status = $ResponseJson->responseStatus;
					throw new Zend_Translate_Exception("Translation failed with msg: $msg ($status)");
				} else {
					throw new Zend_Translate_Exception("Translation failed with unknown error.");
				}
			} else {
				//if not debugging return original string
				return $messageId;
			}
		}
	}

	/**
	 * Load translation data
	 *
	 * @param  string|array  $data
	 * @param  string        $locale  Locale/Language to add data for, identical with locale identifier,
	 *                                see Zend_Locale for more information
	 * @param  array         $options OPTIONAL Options to use
	 */
	protected function _loadTranslationData($data, $locale, array $options = array()) 
	{
		//set locale
		$this->setLocale ($locale);
	}

	/**
	 * returns the adapters name
	 *
	 * @return string
	 */
	public function toString() 
	{
		return "Google";
	}
}
?>