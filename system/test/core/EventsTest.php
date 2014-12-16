<?php
    namespace Test\System;
    use \System\Events;
    use \stdClass;

    /**
     * Test for
     * @see \System\Test
     */
    class EventsTest extends \PHPUnit_Framework_TestCase {
        public static $flag = false;

        public function testEventCall() {
            self::$flag = false;

            $events = new Events();
            $events->on('testEvent', function($data) {
                EventsTest::$flag = $data;
            });

            $events->call('testEvent', true);
            $this->assertTrue(self::$flag, 'event called properly');
        }

        public function testUniqueEventCall() {
            self::$flag = false;

            $events = new Events();
            $events->on('testEvent', function($data) {
                EventsTest::$flag = $data;
            }, true);

            $events->call('testEvent', true);
            $this->assertTrue(self::$flag, 'unique event was called properly');

            $events->call('testEvent', false);
            $this->assertTrue(self::$flag, 'event was not called');
        }

        public function testRemoveEvent() {
            self::$flag = false;

            $events = new Events();
            $events->on('testEvent', function($data) {
                EventsTest::$flag = $data;
            });

            $events->call('testEvent', true);
            $this->assertTrue(self::$flag, 'event was called properly');

            $events->remove('testEvent');

            $events->call('testEvent', false);
            $this->assertTrue(self::$flag, 'event was removed');
        }

        public function testListReturn() {
            $events = new Events();
            $events->on('foo', function() {});
            $events->on('bar', function() {});

            $list = $events->get();

            $this->assertEquals(2, count($list), 'there is 2 events');
            $this->assertEquals('foo', $list[0]->name, 'first one is foo');
            $this->assertEquals('bar', $list[1]->name, 'second one is bar');
        }
    }
?>