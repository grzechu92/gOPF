<?php
	namespace System\Driver;

	/**
	 * Abstract driver class
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	abstract class Driver {
		/**
		 * Driver container name
		 * @var string
		 */
		protected $name;

		/**
		 * Driver lifetime
		 * @var int
		 */
		protected $lifetime;

		/**
		 * Is driver user unique?
		 * @var bool
		 */
		protected $user;

		/**
		 * Initialize driver class
		 *
		 * @param string $name Driver container name
		 * @param int $lifetime Driver lifetime
		 * @param bool $user Is driver user unique?
		 */
		public function __construct($name, $lifetime = 0, $user = false) {
			$this->name = $name;
			$this->lifetime = $lifetime;
			$this->user = $user;
		}

		/**
		 * Generate driver Unique ID
		 *
		 * @return string Driver UID
		 */
		final protected function UID() {
			return sha1(__ID . $this->name . ($this->user ? \System\Core::$UUID : ''));
		}
	}
?>