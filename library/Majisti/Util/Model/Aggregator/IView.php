<?php

namespace Majisti\Util\Model\Aggregator;

/**
 * @desc Interface for classes that can aggregate Zend_View_Interfaces.
 * Serves as a Marker Interface at the same time.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
interface IView
{
    public function getView();
    public function setView(\Zend_View_Interface $view);
}
