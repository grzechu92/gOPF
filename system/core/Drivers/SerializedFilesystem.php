<?php
	namespace System\Drivers;
	use System\Filesystem as FS;
	use System\Serializer;
	use System\Serializer\Exception;
	
	/**
	 * Filesystem driver
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0> 
	 */
	class SerializedFilesystem extends Filesystem implements DriverInterface {
		/**
		 * @see System\Drivers.DriverInterface::set()
		 */
		public function set($content) {
			Serializer::writeFile($this->filename, str_pad(time()+$this->lifetime, self::PAD_SIZE, 0, STR_PAD_LEFT).serialize($content));
		}
		
		/**
		 * @see System\Drivers.DriverInterface::get()
		 */
		public function get() {
			try {
				$content = Serializer::readFile($this->filename);
					
				$lifetime = substr($content, 0, self::PAD_SIZE);
				$data = substr($content, self::PAD_SIZE);
					
				if ($lifetime >= time() && !empty($data)) {
					return unserialize($data);
				}
					
				return null;
			} catch (Exception $exception) {
				return null;
			}
		}
	}
?>