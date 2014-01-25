<?php 
	namespace gOPF\gPAE;
	use \System\Session;
	
	/**
	 * Container class for gPAE session
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Container extends \System\Container {
		/**
		 * Session key
		 * @var string
		 */
		private $key;
		
		/**
		 * Session timeout
		 * @var int
		 */
		private $timeout;
		
		/**
		 * Initiates session container
		 * 
		 * @param string $key Session key
		 * @param int $timeout Session timeout
		 */
		public function __construct($key, $timeout) {
			$this->key = $key;
			$this->timeout = $timeout;
			
			$this->container = Session::get($key);
			Session::set($key, $this->container, $timeout);
			Session::synchronize();
		}
		
		/**
		 * @see \System\Container::set()
		 */
		public function set($offset, $value) {
			$this->container[$offset] = $value;
			Session::set($this->key, $this->container, $this->timeout);
			Session::synchronize();
		}
		
		/**
		 * @see \System\Container::get()
		 */
		public function get($offset) {
			Session::synchronize();
			$this->container = Session::get($this->key);
			return isset($this->container[$offset]) ? $this->container[$offset] : null;
		}
		
		/**
		 * Expires session
		 */
		public function expired() {
			Session::set($this->key, null, 1);
			Session::synchronize();
		}
	}
?>