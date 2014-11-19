<?php
    namespace Test\System;
    use \System\Container;
    use \stdClass;

    class ContainerTest extends \PHPUnit_Framework_TestCase {
        public function testCreateFromArray() {
            $container = new Container(array('foo' => 'bar'));
            $this->assertEquals('bar', $container->get('foo'), 'element created properly from array');
        }

        public function testReadWriteCapability() {
            $container = new Container();

            $container->set('foo1', 'bar1');
            $container->foo2 = 'bar2';

            $this->assertEquals('bar1', $container->get('foo1'), 'check get() method');
            $this->assertEquals('bar2', $container->foo2, 'check __get() method');
        }

        public function testCountableInterface() {
            $container = new Container(array('foo', 'bar'));
            
            $this->assertEquals(2, count($container), 'check countable interface');
        }

        public function testSerializableInterface() {
            $object = new stdClass();
            $object->foo = 'bar';

            $container = new Container();
            $container->foo = 'bar';
            $container->object = $object;

            $serialized = serialize($container);

            $this->assertInternalType('string', $serialized, 'serialized successfully');

            $unserialized = unserialize($serialized);

            $this->assertEquals('bar', $unserialized->foo, 'reading after unserialize');
            $this->assertEquals('bar', $unserialized->object->foo, 'reading after unserialize from nested object');
        }

        public function testIteratorInterface() {
            $container = new Container(array('foo', 'bar'));
            $counter = 0;

            foreach ($container as $element) {
                $counter++;
            }

            $this->assertEquals(2, $counter, 'iterator works');
        }
    }
?>