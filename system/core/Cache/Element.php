<?php
	namespace System\Cache;
	
	/**
	 * Cache element class
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
		public $value;
		
		/**
		 * Element expire time
		 * @var int
		 */
		public $expires;

		/**
		 * Initializes element, saves with in requested data
		 * 
		 * @param string $name Element name
		 * @param mixed $value Element value
		 * @param int $lifetime Element lifetime
		 */
		public function __construct($name, $value, $lifetime) {
			$this->name = $name;
			$this->value = $value;
			$this->expires = $lifetime + time();
		}
	}
?>