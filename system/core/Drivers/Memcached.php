<?php
    namespace System\Drivers;

    /**
     * Memcached driver
     *
     * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
     * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     */
    class Memcached implements DriverInterface {
        /**
         * Memcached default port
         * @var int
         */
        const PORT = 11211;

        /**
         * Memcached default host
         * @var string
         */
        const HOST = '127.0.0.1';

        /**
         * Memcached container lifetime
         * @var int
         */
        public $lifetime = 86400;

        /**
         * Memcached container name prefix
         * @var string
         */
        protected $prefix = 'gOPF-';

        /**
         * Memcached container name
         * @var string
         */
        protected $name;

        /**
         * Memcached core
         * @var \Memcached
         */
        protected $memcached;

        /**
         * @see \System\Drivers\DriverInterface::__construct()
         */
        public function __construct($id, $lifetime = 0) {
            $this->name = $this->prefix.$id;
            $this->lifetime = $lifetime;

            $this->memcached = new \Memcached();
            $this->memcached->addServer(self::HOST, self::PORT);
        }

        /**
         * @see \System\Drivers\DriverInterface::set()
         */
        public function set($content) {
            $this->memcached->set($this->name, $content, $this->lifetime);
        }

        /**
         * @see \System\Drivers\DriverInterface::get()
         */
        public function get() {
            $this->memcached->get($this->name);
        }

        /**
         * @see \System\Drivers\DriverInterface::remove()
         */
        public function remove() {
            $this->memcached->delete($this->name);
        }

        /**
         * @see \System\Drivers\DriverInterface::clear()
         */
        public function clear() {
            $this->memcached->flush();
        }
    }
?>