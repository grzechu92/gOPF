<?php 
	namespace System\Core;

	/**
	 * Core Event class
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Event {
		/**
		 * Code in closure to do in event
		 * @var \Closure
		 */
		public $closure;
		
		/**
		 * Event name
		 * @var string
		 */
		public $name;
		
		/**
		 * Initiates event object
		 * 
		 * @param string $name Event name
		 * @param \Closure $closure Closure to do
		 */
		public function __construct($name, \Closure $closure) {
			$this->name = $name;
			$this->closure = $closure;
		}
		
		/**
		 * Magical method to call closure by calling $this->closure()
		 * 
		 * @param string $method Method name
		 * @param array $args Array with arguments
		 */
		public function __call($method, $args) {
			return call_user_func_array($this->{$method}, $args);
		}
	}
?>