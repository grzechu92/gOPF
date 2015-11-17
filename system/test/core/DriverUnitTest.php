<?php

namespace Test\Core;

use System\Driver;
use System\Driver\Adapter\APC;
use System\Driver\DriverInterface;

class DriverUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testThatDriverIsInstanceable()
    {
        $stub = $this->getAdapterStub();
        $driver = new Driver($stub);

        $this->assertInstanceOf(DriverInterface::class, $driver, 'driver is instanceable');
    }

    public function testThatDriverIsAbleToUseAdapter()
    {
        $stub = $this->getAdapterStub();
        $driver = new Driver($stub);

        $this->assertEquals('foo', $driver->get(), 'driver is able to use adapter');
    }

    /**
     * @return \System\Driver\AdapterInterface
     */
    private function getAdapterStub()
    {
        $stub = $this->getMockBuilder(APC::class)->disableOriginalConstructor()->getMock();

        $stub->method('get')->willReturn('foo');

        return $stub;
    }
}