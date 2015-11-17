<?php

namespace Test\Core;

use \System\Driver;
use \System\Driver\DriverInterface;
use \System\Driver\Exception\UnsupportedMethodException;

class DriverIntegrationTest extends \PHPUnit_Framework_TestCase
{
    public function testAPCAdapter()
    {
        $this->checkAdapter(Driver::APC);
    }

    public function testFilesystemAdapter()
    {
        $this->checkAdapter(Driver::FILESYSTEM);
    }

    public function testSerializedFilesystemAdapter()
    {
        $this->checkAdapter(Driver::SERIALIZED_FILESYSTEM);
    }

    public function testSessionAdapter()
    {
        $this->checkAdapter(Driver::SESSION);
    }

    public function testMemcachedAdapter()
    {
        $this->checkAdapter(Driver::MEMCACHED);
    }

    public function testDatabaseAdapter()
    {
        $this->checkAdapter(Driver::DATABASE);
    }

    private function checkAdapter($adapter)
    {
        $this->checkIfAdapterIsInstanceable($adapter);
        $this->checkAdapterReadAndWrite($adapter);
        $this->checkAdapterSeparation($adapter);
        $this->checkAdapterContentRemoving($adapter);
        $this->checkAdapterCleaning($adapter);

    }

    private function checkIfAdapterIsInstanceable($type)
    {
        $driver = Driver::factory($type, 'testContainer');

        $this->assertInstanceOf(DriverInterface::class, $driver);
    }

    private function checkAdapterReadAndWrite($type)
    {
        $driver = Driver::factory($type, 'testContainer');
        $driver->set('foo');

        $this->assertEquals('foo', $driver->get(), 'adapter IO works as expected');
    }

    private function checkAdapterSeparation($type)
    {
        $driver1 = Driver::factory($type, 'testContainer1');
        $driver2 = Driver::factory($type, 'testContainer2');

        $driver1->set('foo');
        $driver2->set('bar');

        $this->assertEquals('foo', $driver1->get(), 'has propper content');
        $this->assertEquals('bar', $driver2->get(), 'has propper content');
    }

    private function checkAdapterContentRemoving($type)
    {
        $driver = Driver::factory($type, 'testContainer');
        $driver->set('foo');
        $driver->remove();

        $this->assertNull($driver->get(), 'adapter content removed');
    }

    private function checkAdapterCleaning($type)
    {
        $driver1 = Driver::factory($type, 'testContainer1');
        $driver2 = Driver::factory($type, 'testContainer2');

        $driver1->set('foo');
        $driver2->set('bar');

        try {
            $driver1->clear();

            $this->assertNull($driver2->get(), 'adapter clear');
        } catch (UnsupportedMethodException $exception) {}
    }
}