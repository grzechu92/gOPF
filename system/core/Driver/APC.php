<?php
	namespace System\Driver;
	
	/**
	 * APC driver
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0> 
	 */
	class APC extends Driver implements DriverInterface {
		/**
		 * @see \System\Drivers\DriverInterface::set()
		 */
		public function set($content) {
			apc_store($this->UID(), $content, $this->lifetime);
		}
		
		/**
		 * @see \System\Drivers\DriverInterface::get()
		 */
		public function get() {
			$success = false;
			$data = apc_fetch($this->UID(), $success);

			return $success ? $data : null;
		}
		
		/**
		 * @see \System\Drivers\DriverInterface::remove()
		 */
		public function remove() {
			apc_delete($this->UID());
		}
		
		/**
		 * @see \System\Drivers\DriverInterface::clear()
		 */
		public function clear() {
			apc_clear_cache();
		}
	}
?>