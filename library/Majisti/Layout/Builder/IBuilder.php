<?php

namespace Majisti\Layout\Builder;

/**
 * 
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
interface IBuilder extends IBuildable
{
    public function buildDoctype($dom);
    public function buildHtml($dom);
    public function buildHead($dom);
    public function buildBody(IBuilderBody $bodyBuilder, $dom);
}