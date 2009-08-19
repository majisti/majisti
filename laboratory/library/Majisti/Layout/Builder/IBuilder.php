<?php

namespace Majisti\Layout\Builder;

/**
 * 
 * @author Steven Rosato
 */
interface IBuilder extends IBuildable
{
    public function buildDoctype($dom);
    public function buildHtml($dom);
    public function buildHead($dom);
    public function buildBody(IBuilderBody $bodyBuilder, $dom);
}