<?php 
	namespace System\Queue;
	use \System\Queue;
	
	/**
	 * Queue element class
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
		 * Element value
		 * @var mixed
		 */
		public $value;
		
		/**
		 * Initiates queue element
		 * 
		 * @param string $name Element name
		 * @param mixed $value Element value
		 */
		public function __construct($name, $value) {
			$this->name = $name;
			$this->value = $value;
		}
	}
?>