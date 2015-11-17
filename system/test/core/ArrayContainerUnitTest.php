<?php

namespace Test\System;

use \System\ArrayContainer;

class ArrayContainerUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayAccessContainer()
    {
        $container = new ArrayContainer();

        $container['foo'] = 'bar';
        $this->assertEquals('bar', $container['foo'], 'container IO works as expected');

        $this->assertTrue(isset($container['foo']), 'element exists in array');

        unset($container['foo']);

        $this->assertFalse(isset($container['foo']), 'element does not exists in array');
    }
}