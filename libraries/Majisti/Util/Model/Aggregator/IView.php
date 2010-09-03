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
    /**
     * @desc Returns the view.
     * @return \Zend_View The view
     */
    public function getView();

    /**
     * @desc Sets the view.
     * @param Zend_View_Interface $view The view
     */
    public function setView(\Zend_View_Interface $view);
}
