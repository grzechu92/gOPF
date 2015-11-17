<?php

namespace Test\System;

use \System\Container;
use \stdClass;

class ContainerUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateFromArray()
    {
        $container = new Container(array('foo' => 'bar'));
        $this->assertEquals('bar', $container->get('foo'), 'element was created properly from array');
    }

    public function testReadWriteCapability()
    {
        $container = new Container();

        $container->set('foo1', 'bar1');
        $container->foo2 = 'bar2';

        $this->assertEquals('bar1', $container->get('foo1'), 'get() method is working');
        $this->assertEquals('bar2', $container->foo2, '__get() method is working');
    }

    public function testCountableInterface()
    {
        $container = new Container(array('foo', 'bar'));

        $this->assertEquals(2, count($container), 'there are 2 elements');
    }

    public function testSerializableInterface()
    {
        $object = new stdClass();
        $object->foo = 'bar';

        $container = new Container();
        $container->foo = 'bar';
        $container->object = $object;

        $serialized = serialize($container);

        $this->assertInternalType('string', $serialized, 'successfully serialized');

        $unserialized = unserialize($serialized);

        $this->assertEquals('bar', $unserialized->foo, 'read after userialization is successfull');
        $this->assertEquals('bar', $unserialized->object->foo, 'read after unserialize from nested object is successfulk');
    }

    public function testIteratorInterface()
    {
        $container = new Container(array('foo', 'bar'));
        $counter = 0;

        foreach ($container as $element) {
            $counter++;
        }

        $this->assertEquals(2, $counter, 'iterator works perfectly');
    }
}