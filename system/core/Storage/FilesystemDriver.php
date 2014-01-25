<?php
	namespace System\Storage;
	use System\Drivers\DriverInterface;
	use System\Drivers\Filesystem;
	
	/**
	 * Filesystem storage driver
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class FilesystemDriver extends Filesystem implements DriverInterface {
		/**
		 * @see \System\Drivers\DriverInterface::__construct()
		 */
		public function __construct($id, $lifetime) {
			$this->path = __APPLICATION_PATH.DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'storage';
			$this->filename = $this->path.DIRECTORY_SEPARATOR.$id;
			$this->lifetime = $lifetime;
		}
	}
?>