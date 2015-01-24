<?php
	namespace System\Driver;

	/**
	 * Memcached driver
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Memcached extends Driver implements DriverInterface {
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
		 * Memcached core
		 * @var \Memcached
		 */
		protected $memcached;

		/**
		 * @see \System\Drivers\DriverInterface::__construct()
		 */
		public function __construct($name, $lifetime = 0, $user = false) {
			$this->name = $name;
			$this->lifetime = $lifetime;
			$this->user = $user;

			$this->memcached = new \Memcached();
			$this->memcached->addServer(self::HOST, self::PORT);
		}

		/**
		 * @see \System\Drivers\DriverInterface::set()
		 */
		public function set($content) {
			$this->memcached->set($this->UID(), $content, $this->lifetime);
		}

		/**
		 * @see \System\Drivers\DriverInterface::get()
		 */
		public function get() {
			return $this->memcached->get($this->UID());
		}

		/**
		 * @see \System\Drivers\DriverInterface::remove()
		 */
		public function remove() {
			$this->memcached->delete($this->UID());
		}

		/**
		 * @see \System\Drivers\DriverInterface::clear()
		 */
		public function clear() {
			$this->memcached->flush();
		}
	}
?>