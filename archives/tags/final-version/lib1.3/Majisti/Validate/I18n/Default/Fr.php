<?php

/**
 * TODO: doc
 * TODO: finish translation
 *
 * @author Steven Rosato
 */
class Majisti_Validate_I18n_Default_Fr extends Majisti_Validate_I18n_Abstract
{
	protected $_zendMessagesTemplates = array(
//		'notAlnum' 						=> 'Not Translated Yet',
		'stringEmpty' 					=> 'La chaine ne peut être vide',
		'notAlpha' 						=> 'Le champ doit être alphabétique',
		'notBetween' 					=> '%value% doit étre entre %min% et %max%, inclusivement',
		'notBetweenStrict' 				=> '%value% doit étre entre %min% et %max%, exclusivement',
		'ccnumLength' 					=> '%value% doit contenir entre 13 et 19 chiffres',
		'ccnumChecksum' 				=> "L'algorithme Luhn (mod-10 checksum) échouée sur %value%",
		'dateNotYYYY-MM-DD' 			=> '%value% doit étre de format YYYY-MM-DD',
		'dateInvalid' 					=> 'La date est invalide',
		'dateFalseFormat' 				=> "La date n'est pas du bon format",
		'notDigits' 					=> 'La chaine doit contenir seulement des chiffres',
		'emailAddressInvalid' 			=> "L'addresse courriel n'est pas du bon format",
		'emailAddressInvalidHostname' 	=> "%hostname% n'est un nom d'héte valide pour l'adresse courriel %value%",
		'emailAddressInvalidMxRecord' 	=> "%hostname% ne semble pas avoir un enregistrement MX valide pour l'adresse courriel %value%",
//		'emailAddressDotAtom' 			=> 'Not Translated Yet',
//		'emailAddressQuotedString' 		=> 'Not Translated Yet',
//		'emailAddressInvalidLocalPart' 	=> 'Not Translated Yet',
		'notFloat' 						=> 'La valeur doit étre de type flottante (float)',
		'notGreaterThan'				=> '%value% doit être plus grand que %min%',
//		'notHex' 						=> 'Not Translated Yet',
//		'hostnameIpAddressNotAllowed' 	=> 'Not Translated Yet',
//		'hostnameUnknownTld' 			=> 'Not Translated Yet',
//		'hostnameDashCharacter' 		=> 'Not Translated Yet',
//		'hostnameInvalidHostnameSchema' => 'Not Translated Yet',
//		'hostnameUndecipherableTld' 	=> 'Not Translated Yet',
//		'hostnameInvalidHostname' 		=> 'Not Translated Yet',
//		'hostnameInvalidLocalName' 		=> 'Not Translated Yet',
//		'hostnameLocalNameNotAllowed' 	=> 'Not Translated Yet',
//		'notSame' 						=> 'Not Translated Yet',
//		'missingToken'					=> 'Not Translated Yet',
		'notInArray' 					=> 'Vous devez choisir une option',
//		'notInt' 						=> 'Not Translated Yet',
//		'notIpAddress' 					=> 'Not Translated Yet',
//		'notLessThan' 					=> 'Not Translated Yet',
		'isEmpty' 						=> "L'élément est requis",
//		'regexNotMatched' 				=> 'Not Translated Yet',
		'stringLengthTooShort' 			=> 'La chaîne doit contenir un minimum de %min% caractères',
		'stringLengthTooLong' 			=> 'La chaîne doit contenir un maximum de %max% caractères',

		//captchas (WORD)
		'missingValue'					=> 'Not Translated Yet',
		'missingID'						=> 'Not Translated Yet',
		'badCaptcha'					=> 'La valeur entrée est incorrecte',
	);

	protected $_majistiMessagesTemplates = array(
		/* Form error count message */
		'form_errors_count' => 'Vous avez %errorsCount% erreur(s) à corriger.',
	
		/* Majisti's Default Decorator */
		'mendatory_tooltip'		=> 'Champ obligatoire',

		/* Majisti's Validators */
		'isNotPhone' 				=> 'Numéro de téléphone invalide',
		'isNotPhoneCountry'			=> "Le téléphone ne semble pas étre valide pour le pays %country%",
		'isDigits'					=> 'Le champ ne peut contenir de caractéres numériques',
		'isOnlyCharacters'			=> 'Le champ ne peut contenir seulement des caractères',
		'notOnlySpecialChars' 		=> 'Le champ ne peut pas contenir seulement des caractères spéciaux',
		'invalidPostalCode'			=> 'Le code postal est invalide',
		'invalidPostalCodeCountry' 	=> 'Le code postal pour %country% est invalide.'
	);
}