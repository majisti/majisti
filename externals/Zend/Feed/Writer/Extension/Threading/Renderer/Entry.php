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
 * @category   Zend
 * @package    Zend_Feed_Writer
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Entry.php 19730 2009-12-17 22:28:45Z padraic $
 */
 
/**
 * @see Zend_Feed_Writer_Extension_RendererAbstract
 */
require_once 'Zend/Feed/Writer/Extension/RendererAbstract.php';
 
/**
 * @category   Zend
 * @package    Zend_Feed_Writer
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Writer_Extension_Threading_Renderer_Entry
    extends Zend_Feed_Writer_Extension_RendererAbstract
{
    /**
     * Render entry
     * 
     * @return void
     */
    public function render()
    {
        if (strtolower($this->getType()) == 'rss') {
            return; // Atom 1.0 only
        }
        $this->_appendNamespaces();
        $this->_setCommentLink($this->_dom, $this->_base);
        $this->_setCommentFeedLinks($this->_dom, $this->_base);
        $this->_setCommentCount($this->_dom, $this->_base);
    }
    
    /**
     * Append entry namespaces
     * 
     * @return void
     */
    protected function _appendNamespaces()
    {
        $this->getRootElement()->setAttribute('xmlns:thr',
            'http://purl.org/syndication/thread/1.0');  
    }
    
    /**
     * Set comment link
     * 
     * @param  DOMDocument $dom 
     * @param  DOMElement $root 
     * @return void
     */
    protected function _setCommentLink(DOMDocument $dom, DOMElement $root)
    {
        $link = $this->getDataContainer()->getCommentLink();
        if (!$link) {
            return;
        }
        $clink = $this->_dom->createElement('link');
        $clink->setAttribute('rel', 'replies');
        $clink->setAttribute('type', 'text/html');
        $clink->setAttribute('href', $link);
        $count = $this->getDataContainer()->getCommentCount();
        if (!$count) {
            $count = 0;
        }
        $clink->setAttribute('thr:count', $count);
        $root->appendChild($clink);
    }
    
    /**
     * Set comment feed links
     * 
     * @param  DOMDocument $dom 
     * @param  DOMElement $root 
     * @return void
     */
    protected function _setCommentFeedLinks(DOMDocument $dom, DOMElement $root)
    {
        $links = $this->getDataContainer()->getCommentFeedLinks();
        if (!$links || empty($links)) {
            return;
        }
        foreach ($links as $link) {
            $flink = $this->_dom->createElement('link');
            $flink->setAttribute('rel', 'replies');
            $flink->setAttribute('type', 'application/'. $link['type'] .'+xml');
            $flink->setAttribute('href', $link['uri']);
            $count = $this->getDataContainer()->getCommentCount();
            if (!$count) {
                $count = 0;
            }
            $flink->setAttribute('thr:count', $count);
            $root->appendChild($flink);
        }
    }

    /**
     * Set entry comment count
     * 
     * @param  DOMDocument $dom 
     * @param  DOMElement $root 
     * @return void
     */
    protected function _setCommentCount(DOMDocument $dom, DOMElement $root)
    {
        $count = $this->getDataContainer()->getCommentCount();
        if (!$count) {
            $count = 0;
        }
        $tcount = $this->_dom->createElement('thr:total');
        $tcount->nodeValue = $count;
        $root->appendChild($tcount);
    }
}
