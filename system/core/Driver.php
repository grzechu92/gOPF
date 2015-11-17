<?php

namespace System;

use System\Driver\AdapterInterface;
use System\Driver\DriverInterface;
use System\Driver\Exception\UnknownAdapterException;

/**
 * Framework driver manager.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Driver implements DriverInterface
{
    /**
     * APC based driver.
     *
     * @var string
     */
    const APC = 'APC';

    /**
     * Session based driver.
     *
     * @var string
     */
    const SESSION = 'Session';

    /**
     * Filesystem based driver.
     *
     * @var string
     */
    const FILESYSTEM = 'Filesystem';

    /**
     * Memcached based driver.
     *
     * @var string
     */
    const MEMCACHED = 'Memcached';

    /**
     * Serialized filesystem based driver.
     *
     * @var string
     */
    const SERIALIZED_FILESYSTEM = 'SerializedFilesystem';

    /**
     * Database based driver.
     *
     * @var string
     */
    const DATABASE = 'Database';

    /**
     * Driver adapter
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * Create specified driver instance.
     *
     * @param string $type     Driver type (Driver::APC, Driver::SESSION, Driver::FILESYSTEM, Driver::MEMCACHED, Driver::SERIALIZED_FILESYSTEM)
     * @param string $name     Driver container name
     * @param int    $lifetime Driver container lifetime
     * @param bool   $user     Is driver user unique?
     *
     * @return \System\Driver\DriverInterface Specified driver instance
     * @throws \System\Driver\Exception\UnknownAdapterException
     */
    public static function factory($type, $name, $lifetime = 0, $user = false)
    {
        $adapterClass = '\\System\\Driver\\Adapter\\' . $type;
        $adapter = null;

        try {
            $adapter = new $adapterClass($name, $lifetime, $user);
        } catch (\System\Loader\Exception $exception) {
            throw new UnknownAdapterException('Unable to find driver adapter ' . $adapterClass, 500);
        }

        return new self($adapter);
    }

    /**
     * @see \System\Driver\DriverInterface::__construct()
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @see \System\Driver\DriverInterface::set()
     */
    public function set($content)
    {
        $this->adapter->set($content);
    }

    /**
     * @see \System\Driver\DriverInterface::get()
     */
    public function get()
    {
        return $this->adapter->get();
    }

    /**
     * @see \System\Driver\DriverInterface::remove()
     */
    public function remove()
    {
        $this->adapter->remove();
    }

    /**
     * @see \System\Driver\DriverInterface::clear()
     */
    public function clear()
    {
        $this->adapter->clear();
    }
}
