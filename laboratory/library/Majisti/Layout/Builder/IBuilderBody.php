<?php

namespace Majisti\Layout\Builder;

/**
 * 
 * @author Steven Rosato
 */
interface IBuilderBody extends IBuildable
{
    public function buildContainer($content);
    public function buildHeader($content);
    public function buildContent($content);
    public function buildFooter($content);
}
