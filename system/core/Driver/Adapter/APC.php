<?php

namespace System\Driver\Adapter;

use System\Driver\AbstractAdapter;
use System\Driver\AdapterInterface;

/**
 * APC driver.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class APC extends AbstractAdapter implements AdapterInterface
{
    /**
     * @see \System\Drivers\AdapterInterface::set()
     */
    public function set($content)
    {
        apc_store($this->UID(), $content, $this->lifetime);
    }

    /**
     * @see \System\Drivers\AdapterInterface::get()
     */
    public function get()
    {
        if (apc_exists($this->UID())) {
            return apc_fetch($this->UID());
        }

        return null;
    }

    /**
     * @see \System\Drivers\AdapterInterface::remove()
     */
    public function remove()
    {
        apc_delete($this->UID());
    }

    /**
     * @see \System\Drivers\AdapterInterface::clear()
     */
    public function clear()
    {
        apc_clear_cache();
    }
}
