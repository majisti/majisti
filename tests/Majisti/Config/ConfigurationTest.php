<?php

namespace Majisti\Config;

require_once __DIR__ . '/TestHelper.php';

/**
 * @desc Test class for the Configuration class.
 *
 * @author Steven Rosato
 */
class ConfigurationTest extends \Majisti\Test\TestCase
{
    public $options = array(
        'foo' => 'foo',
        'bar' => 'bar',
        'bax' => false,
        'nested' => array(
            'foo' => 'foo',
        )
    );

    public $defaultOptions = array(
        'foo' => 'fooDefault',
        'baz' => 'bazDefault',
        'nested' => array(
            'foo' => 'fooDefault',
            'bar' => 'barDefault',
        )
    );

    public function testConstructor()
    {
        $config = new Configuration($this->options, $this->defaultOptions);

        $this->assertEquals('foo', $config->find('foo'));
        $this->assertEquals('bazDefault', $config->find('baz'));

        $this->assertEquals('foo', $config->find('nested.foo'));
        $this->assertEquals('barDefault', $config->find('nested.bar'));
    }

    public function testOnlyDefaultOptionsPassedToConstructor()
    {
        $config = new Configuration($this->defaultOptions);

        $this->assertFalse($config->has('bar'));
        $this->assertFalse($config->has('nested.baz'));

        $this->assertEquals($this->defaultOptions, $config->getOptions()->toArray());
    }

    public function testOnlyOptionsPassedToConstructor()
    {
        $config = new Configuration($this->options);

        $this->assertFalse($config->has('baz'));
        $this->assertFalse($config->has('nested.bar'));

        $this->assertEquals($this->options, $config->getOptions()->toArray());
    }

    public function testHas()
    {
        $config = new Configuration($this->options, $this->defaultOptions);

        $this->assertTrue($config->has('foo'));
        $this->assertFalse($config->has('notThere'));

        $this->assertTrue($config->has('bax'));
    }

    public function testHasOnMultipleSelections()
    {
        $config = new Configuration($this->options, $this->defaultOptions);

        $this->assertTrue($config->has(array(
            'foo',
            'baz',
            'nested.foo',
            'nested.bar'
        )));

        $this->assertFalse($config->has(array(
            'foo',
            'notThere',
        )));
    }

    public function testExtend()
    {
        $config = new Configuration($this->options, $this->defaultOptions);

        $config->extend(array('test' => 'test'));
        $this->assertTrue($config->has('test'));

        $config->extend(array('test2' => 'test2'));
        $this->assertTrue($config->has('test'));
        $this->assertTrue($config->has('test2'));
    }

    public function testOptionsTypes()
    {
        $config = new Configuration(
            new \Zend_Config($this->options),
            new \Zend_Config($this->defaultOptions)
        );

        $this->assertTrue($config->has('foo'));
        $this->assertTrue($config->has('baz'));

        $config = new Configuration(
            new Configuration($this->options),
            new Configuration($this->defaultOptions)
        );
        $this->assertTrue($config->has('foo'));
        $this->assertTrue($config->has('baz'));

        $config->extend(new Configuration(array('test' => 'test')));
        $this->assertTrue($config->has('test'));
    }

    public function testReset()
    {
        $config = new Configuration($this->options, $this->defaultOptions);

        $config->extend(array('test' => 'test'))
               ->clearOptions();

        $this->assertEquals($this->defaultOptions,
            $config->getOptions()->toArray());
    }

    /**
     * @expectedException Exception
     */
    public function testExceptionThrowOnWrongOptionsFormatInConstructor()
    {
        new Configuration('foo', $this->defaultOptions);
    }

    /**
     * @expectedException Exception
     */
    public function testExceptionThrowOnWrongDefaultOptionsFormatInConstructor()
    {
        new Configuration($this->options, 'foo');
    }

    /**
     * @expectedException Exception
     */
    public function testExceptionThrowOnDefaultOptions()
    {
        $config = new Configuration($this->options, $this->defaultOptions);

        $config->setDefaultOptions('foo');
    }

    /**
     * @expectedException Exception
     */
    public function testExceptionThrowOnWrongExtend()
    {
        $config = new Configuration($this->options, $this->defaultOptions);

        $config->extend('foo');
    }
}

ConfigurationTest::runAlone();
