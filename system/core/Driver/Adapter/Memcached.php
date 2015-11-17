<?php

namespace System\Driver\Adapter;

use System\Driver\AbstractAdapter;
use System\Driver\AdapterInterface;

/**
 * Memcached driver.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Memcached extends AbstractAdapter implements AdapterInterface
{
    /**
     * Memcached default port.
     *
     * @var int
     */
    const PORT = 11211;

    /**
     * Memcached default host.
     *
     * @var string
     */
    const HOST = '127.0.0.1';

    /**
     * Memcached core.
     *
     * @var \Memcached
     */
    protected $memcached;

    /**
     * @see \System\Drivers\AdapterInterface::__construct()
     */
    public function __construct($name, $lifetime = 0, $user = false)
    {
        parent::__construct($name, $lifetime, $user);

        $this->memcached = new \Memcached();
        $this->memcached->addServer(self::HOST, self::PORT);
    }

    /**
     * @see \System\Drivers\AdapterInterface::set()
     */
    public function set($content)
    {
        $this->memcached->set($this->UID(), $content, $this->lifetime);
    }

    /**
     * @see \System\Drivers\AdapterInterface::get()
     */
    public function get()
    {
        $data = $this->memcached->get($this->UID());

        if ($this->memcached->getResultCode() == \Memcached::RES_NOTFOUND) {
            return null;
        } else {
            return $data;
        }
    }

    /**
     * @see \System\Drivers\AdapterInterface::remove()
     */
    public function remove()
    {
        $this->memcached->delete($this->UID());
    }

    /**
     * @see \System\Drivers\AdapterInterface::clear()
     */
    public function clear()
    {
        $this->memcached->flush();
    }
}
