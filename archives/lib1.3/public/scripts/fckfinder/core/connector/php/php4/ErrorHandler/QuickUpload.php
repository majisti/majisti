<?php
/*
 * CKFinder
 * ========
 * http://www.ckfinder.com
 * Copyright (C) 2007-2008 Frederico Caldeira Knabben (FredCK.com)
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */

/**
 * @package CKFinder
 * @subpackage ErrorHandler
 * @copyright Frederico Caldeira Knabben
 */

/**
 * Include base error handling class
 */
require_once CKFINDER_CONNECTOR_LIB_DIR . "/ErrorHandler/Base.php";

/**
 * File upload error handler
 * 
 * @package CKFinder
 * @subpackage ErrorHandler
 * @copyright Frederico Caldeira Knabben
 */
class CKFinder_Connector_ErrorHandler_QuickUpload extends CKFinder_Connector_ErrorHandler_Base
{
    /**
     * Throw file upload error, return true if error has been thrown, false if error has been catched
     *
     * @param int $number
     * @param string $text
     * @access public
     */
    function throwError($number, $text = false, $exit = true)
    {
        if ($this->_catchAllErrors || in_array($number, $this->_skipErrorsArray)) {
            return false;
        }

        $oRegistry =& CKFinder_Connector_Core_Factory::getInstance("Core_Registry");
        $sFileName = $oRegistry->get("FileUpload_fileName");
        $sFileUrl = $oRegistry->get("FileUpload_url");

        echo "<script type=\"text/javascript\">";
        if (empty($text)) {
            echo "window.parent.OnUploadCompleted(" . $number . ", '', '', '') ;";
        }
        else {
            echo "window.parent.OnUploadCompleted(" . $number . ", '" .str_replace("'", "\\'", $sFileUrl . $sFileName). "', '" . str_replace("'", "\\'", $sFileName) . "', '') ;";
        }
        echo "</script>";

        if ($exit) {
            exit;
        }
    }    
}