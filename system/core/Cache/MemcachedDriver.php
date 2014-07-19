<?php
    namespace System\Cache;
    use System\Drivers\DriverInterface;
    use System\Drivers\Memcached;

    /**
     * Memcached cache driver
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
    class MemcachedDriver extends Memcached implements DriverInterface {
        /**
         * @see \System\Drivers\Memcached::$prefix
         */
        protected $prefix = 'gOPF-CACHE-';
    }
?>