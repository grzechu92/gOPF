<?php
	namespace System\Drivers;
	
	/**
	 * APC driver
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0> 
	 */
	class APC implements DriverInterface {
		/**
		 * APC container lifetime
		 * @var int
		 */
		public $lifetime = 86400;
		
		/**
		 * APC container name prefix
		 * @var string
		 */
		protected $prefix = 'gOPF-';
		
		/**
	 	 * APC container name
		 * @var string
		 */
		protected $name;
		
		/**
		 * @see System\Drivers.DriverInterface::__construct()
		 */
		public function __construct($id, $lifetime = 0) {
			$this->name = $this->prefix.$id;
			$this->lifetime = $lifetime;
		}
		
		/**
		 * @see System\Drivers.DriverInterface::set()
		 */
		public function set($content) {
			apc_store($this->name, $content, $this->lifetime);
		}
		
		/**
		 * @see System\Drivers.DriverInterface::get()
		 */
		public function get() {
			return apc_fetch($this->name);
		}
		
		/**
		 * @see System\Drivers.DriverInterface::remove()
		 */
		public function remove() {
			apc_delete($this->name);
		}
		
		/**
		 * @see System\Drivers.DriverInterface::clear()
		 */
		public function clear() {
			apc_clear_cache();
		}
	}
?>