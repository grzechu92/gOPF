<?php
	namespace System\Driver;
	use System\Filesystem as FS;
	use System\Serializer;
	use System\Serializer\Exception;
	
	/**
	 * Filesystem driver
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0> 
	 */
	class SerializedFilesystem extends Filesystem implements DriverInterface {
		/**
		 * @see \System\Drivers\DriverInterface::set()
		 */
		public function set($content) {
			Serializer::write($this->filename, str_pad((($this->lifetime > 0) ? time() + $this->lifetime : 0), self::PAD_SIZE, 0, STR_PAD_LEFT).serialize($content));
		}
		
		/**
		 * @see \System\Drivers\DriverInterface::get()
		 */
		public function get() {
			try {
				$content = Serializer::read($this->filename);

				$lifetime = substr($content, 0, self::PAD_SIZE);
				$data = substr($content, self::PAD_SIZE);

                if ($lifetime == 0 || $lifetime >= time() && !empty($data)) {
					return unserialize($data);
				}

				return null;
			} catch (Exception $exception) {
				return null;
			}
		}
	}
?>