<?php
	namespace System\Driver\Session;

	/**
	 * Session driver element
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Element {
		/**
		 * Element name
		 * @var string
		 */
		public $name;

		/**
		 * Element lifetime
		 * @var int
		 */
		private $lifetime;

		/**
		 * Element value
		 * @var mixed
		 */
		private $value;

		/**
		 * Initialize session element
		 *
		 * @param string $name Element name
		 * @param int $lifetime Element lifetime
		 */
		public function __construct($name, $lifetime) {
			$this->name = $name;
			$this->lifetime = $lifetime;
		}

		/**
		 * Get element value
		 *
		 * @return mixed Element value
		 */
		public function get() {
			return $this->value;
		}

		/**
		 * Set element value
		 *
		 * @param mixed $value Element value
		 */
		public function set($value) {
			$this->value = $value;
		}

		/**
		 * Is element valid?
		 *
		 * @return bool Is valid?
		 */
		public function isValid() {
			return $this->lifetime == 0 || $this->lifetime >= time();
		}
	}
?>