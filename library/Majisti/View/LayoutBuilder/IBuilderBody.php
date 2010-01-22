<?php

namespace Majisti\View\LayoutBuilder;

/**
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
interface IBuilderBody extends IBuildable
{
    public function buildContainer($content);
    public function buildHeader($content);
    public function buildContent($content);
    public function buildFooter($content);
}
