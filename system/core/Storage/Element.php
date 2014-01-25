<?php
	namespace System\Storage;
	
	/**
	 * Storage element class
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Element {
		/**
		 * Element name
		 * @var string
		 */
		public $name;
		
		/**
		 * Element value
		 * @var mixed
		 */
		private $value;
		
		/**
		 * Element driver
		 * @var \System\Drivers\DriverInterface
		 */
		private $driver;
		
		/**
		 * Whether to save element?
		 * @var bool
		 */
		public $save = false;
		
		/**
		 * Is temporary element?
		 * @var bool
		 */
		public $temporary = false;
		
		
		/**
		 * Initializes element, saves with in requested data
		 * 
		 * @param string $name Storage element name
		 * @param mixed $value Storage element value
		 * @param \System\Drivers\DriverInterface $driver Storage element driver
		 */
		public function __construct($name, $value, \System\Drivers\DriverInterface $driver) {
			$this->name = $name;
			$this->value = $value;
			$this->driver = $driver;
			
			$this->read();
		}
		
		/**
		 * Saves element if required
		 */
		public function __destruct() {
			if (!$this->temporary && empty($this->value)) {
				$this->remove();
				
				return false;
			}
			
			if ($this->save && !$this->temporary) {
				$this->write();
			}
		}
		
		/**
		 * Reads value of element from driver
		 */
		public function read() {
			$this->value = $this->driver->get();
		}
		
		/**
		 * Saves value into driver
		 */
		public function write() {
			$this->driver->set($this->value);
			$this->save = false;
		}
		
		/**
		 * Sets new value of element
		 * 
		 * @param mixed $value Element value
		 */
		public function set($value) {
			$this->save = true;
			$this->value = $value;
		}
		
		/**
		 * Returns element value
		 * 
		 * @return mixed Element value
		 */
		public function get() {
			return $this->value;
		}
		
		/**
		 * Removes driver
		 */
		public function remove () {
			$this->driver->remove();
			$this->save = false;
		}
	}
?>