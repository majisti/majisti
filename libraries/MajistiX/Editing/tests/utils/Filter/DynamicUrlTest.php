<?php

namespace MajistiX\Editing\Util\Filter;

use \Majisti\Config\Configuration;

require_once __DIR__ . '/TestHelper.php';

class DynamicUrlTest extends \Majisti\Test\TestCase
{
    /**
     * @var Configuration 
     */
    public $conf;

    public $filter;

    public function setUp()
    {
        $this->conf = new Configuration(
            $this->getHelper()->createBootstrapInstance()->getOptions());

        $this->filter = new DynamicUrl($this->conf);
    }

    public function testFilter()
    {
        $filter = $this->filter;

        $value = "{$this->conf->find('majisti.app.url')}/foo.jpg";
        $this->assertEquals("##{majisti.app.url}##/foo.jpg", $filter->filter($value));

        $value = "{$this->conf->find('majisti.url')}/foo.jpg";
        $this->assertEquals("##{majisti.url}##/foo.jpg", $filter->filter($value));

        $value = 'http://google.ca/foo.jpg';
        $this->assertEquals($value, $filter->filter($value));
    }
}

DynamicUrlTest::runAlone();
