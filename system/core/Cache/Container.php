<?php
	namespace System\Cache;

	class Container {
		/**
		 * User cache type
		 * @var int
		 */
		const USER = 1;

		/**
		 * Global cache type
		 * @var int
		 */
		const COMMON = 2;

		/**
		 * Runtime cache type (only while executing script)
		 * @var int
		 */
		const RUNTIME = 3;

		/**
		 * Cache container elements
		 * @var \System\Cache\Element[]
		 */
		private $elements = array();

		/**
		 * Is container value changed?
		 * @var bool
		 */
		private $changed = false;

		/**
		 * Container driver
		 * @var \System\Driver\DriverInterface
		 */
		private $driver;

		/**
		 * Container type
		 * @var int
		 */
		private $type;

		/**
		 * Initialize cache container
		 *
		 * @param int $type Container type
		 * @param \System\Config\File $config Cache configuration
		 */
		public function __construct($type, \System\Config\File $config) {
			$this->type = $type;

			if ($this->type != self::RUNTIME) {
				$this->driver = \System\Driver::factory($config->driver, 'CACHE', $config->lifetime, ($type == self::USER));
				$this->elements = $this->driver->get();
			}
		}

		/**
		 * Clean cache and save it
		 */
		public function __destruct() {
			if ($this->type != self::RUNTIME) {
				$this->clean();

				if ($this->changed) {
					$this->driver->set($this->elements);
				}
			}
		}

		/**
		 * Set container element value
		 *
		 * @param string $name Element name
		 * @param mixed $value Element value
		 * @param int $expires Element lifetime
		 */
		public function set($name, $value, $expires) {
			$this->changed = true;

			$this->elements[$name] = new Element($name, $value, $expires);
		}

		/**
		 * Return cache element value
		 *
		 * @param string $name Element name
		 * @return mixed Element value
		 */
		public function get($name) {
			if (!isset($this->elements[$name])) {
				return null;
			} else {
				return $this->elements[$name]->value;
			}
		}

		/**
		 * Is cache element valid?
		 *
		 * @param string $name Element name
		 * @return bool Is valid?
		 */
		public function isValid($name) {
			if (!isset($this->elements[$name])) {
				return false;
			} else {
				return $this->elements[$name]->expires >= time();
			}
		}

		/**
		 * Remove cache element
		 *
		 * @param string $name Element name
		 */
		public function remove($name) {
			unset($this->elements[$name]);
			$this->changed = true;
		}

		/**
		 * Clear cache container
		 */
		public function clear() {
			$this->elements = array();
			$this->changed = true;
		}

		/**
		 * Clean cache from expired elements
		 */
		public function clean() {
			if (count($this->elements) > 0) {
				foreach ($this->elements as $element) {
					if (!$this->isValid($element->name)) {
						unset($this->elements[$element->name]);

						$this->changed = true;
					}
				}
			}
		}
	}
?>