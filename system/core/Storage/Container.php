<?php

namespace System\Storage;

/**
 * Storage container class.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Container
{
    /**
     * Element name.
     *
     * @var string
     */
    public $name;

    /**
     * Element value.
     *
     * @var mixed
     */
    private $value;

    /**
     * Element driver.
     *
     * @var \System\Driver\DriverInterface
     */
    private $driver;

    /**
     * Whether to save element?
     *
     * @var bool
     */
    public $save = false;

    /**
     * Is temporary element?
     *
     * @var bool
     */
    public $temporary = false;

    /**
     * Initializes container, saves with in requested data.
     *
     * @param string                         $name   Storage element name
     * @param mixed                          $value  Storage element value
     * @param \System\Driver\DriverInterface $driver Storage element driver
     */
    public function __construct($name, $value, \System\Driver\DriverInterface $driver)
    {
        $this->name = $name;
        $this->value = $value;
        $this->driver = $driver;

        $this->read();
    }

    /**
     * Saves container if required.
     */
    public function __destruct()
    {
        if (!$this->temporary && empty($this->value)) {
            $this->remove();

            return false;
        }

        if ($this->save && !$this->temporary) {
            $this->write();
        }
    }

    /**
     * Reads value of container from driver.
     */
    public function read()
    {
        $this->value = $this->driver->get();
    }

    /**
     * Saves value into driver.
     */
    public function write()
    {
        $this->driver->set($this->value);
        $this->save = false;
    }

    /**
     * Sets new value of container.
     *
     * @param mixed $value Container value
     */
    public function set($value)
    {
        $this->save = true;
        $this->value = $value;
    }

    /**
     * Returns container value.
     *
     * @return mixed Container value
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * Removes driver.
     */
    public function remove()
    {
        $this->driver->remove();
        $this->save = false;
    }
}
