<?php
    namespace Test\System;
    use \System\Driver;

    /**
     * Test for
     * @see \System\Driver
     */
    class DriverTest extends \PHPUnit_Framework_TestCase {
        public function testAPCDriver() {
            $this->checkSpecifiedDriver(Driver::APC);
        }

        public function testFilesystemDriver() {
            $this->checkSpecifiedDriver(Driver::FILESYSTEM);
        }

        public function testSerializedFilesystemDriver() {
            $this->checkSpecifiedDriver(Driver::SERIALIZED_FILESYSTEM);
        }

        public function testSessionDriver() {
            $this->checkSpecifiedDriver(Driver::SESSION);
        }

        public function testMemcachedDriver() {
            $this->checkSpecifiedDriver(Driver::MEMCACHED);
        }

        public function testDatabaseDriver() {
            $this->checkSpecifiedDriver(Driver::DATABASE);
        }

        private function checkSpecifiedDriver($type) {
            $container = Driver::factory($type, 'foo');
            $preserve = Driver::factory($type, 'test');
            $preserve->set('test');

            $this->assertTrue($container instanceof \System\Driver\DriverInterface, 'instance was created successfully');

            $this->assertNull($container->set('bar'), 'container value can be set');
            $this->assertEquals('bar', $container->get(), 'container has expected value');

            $this->assertNull($container->remove(), 'container has been removed');
            $this->assertNull($container->get(), 'container has expected value after removing');

            $this->assertEquals('test', $preserve->get(), 'another container preserves value');

            try {
                $this->assertNull($container->clear(), 'remove all driver containers');
                $this->assertNull($preserve->get(), 'container has expected value after removing');
            } catch (\System\Core\Exception $e) {}
        }
    }
?>