<?php

namespace System;

/**
 * Class which allows to call object instance like array.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class ArrayContainer extends Container implements \ArrayAccess
{
    /**
     * Array wrapper for set() method.
     *
     * @param string $offset Variable name
     * @param mixed  $value  Variable value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Array wrapper for get() method.
     *
     * @param string $offset Variable name
     *
     * @return mixed Variable value
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Checks if any variable with specified name exists in container.
     *
     * @param string $offset Variable name
     *
     * @return bool Exist or not
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Deletes specified variable from container.
     *
     * @param string $offset Variable name
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }
}
