<?php

namespace System\Driver;

use System\Driver;

/**
 * Interface which describes how to write drivers for framework modules.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
interface AdapterInterface extends CrudInterface
{
    /**
     * Saves data into driver.
     *
     * @param string $name     Driver container name
     * @param int    $lifetime Driver container lifetime
     * @param bool   $user     Driver content depends on user?
     */
    public function __construct($name, $lifetime = 0, $user = false);
}
