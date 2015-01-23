<?php
	namespace System\Driver;
	use System\Filesystem as FS;
	
	/**
	 * Filesystem driver
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0> 
	 */
	class Filesystem extends Driver implements DriverInterface {
		/**
		 * Lifetime metadata pad size
		 * @var int
		 */
		const PAD_SIZE = 15;
		
		/**
		 * Filesystem container name
	 	 * @var string
		 */
		protected $filename;

        /**
         * Filesystem directory
         * @var
         */
        private $path;
		
		/**
		 * @see \System\Drivers\DriverInterface::__construct()
		 */
        public function __construct($name, $lifetime = 0, $user = false) {
            $this->name = $name;
            $this->lifetime = $lifetime;
            $this->user = $user;

            $this->path = __VARIABLE_PATH.DIRECTORY_SEPARATOR;
            $this->filename = $this->path.$this->UID();
        }

		/**
		 * @see \System\Drivers\DriverInterface::set()
		 */
		public function set($content) {
			FS::write($this->filename, str_pad((($this->lifetime > 0) ? time() + $this->lifetime : 0), self::PAD_SIZE, 0, STR_PAD_LEFT).serialize($content), true);
		}
		
		/**
		 * @see \System\Drivers\DriverInterface::get()
		 */
		public function get() {
			$content = FS::read($this->filename, true);
			
			$lifetime = substr($content, 0, self::PAD_SIZE);
			$data = substr($content, self::PAD_SIZE);
			
			if ($lifetime == 0 || $lifetime >= time() && !empty($data)) {
				return unserialize($data);
			}
			
			return null;
		}
		
		/**
		 * @see \System\Drivers\DriverInterface::remove()
		 */
		public function remove() {
			FS::remove($this->filename);
		}
		
		/**
		 * @see \System\Drivers\DriverInterface::clear()
		 */
		public function clear() {
			FS::remove($this->path, true);
			FS::mkdir($this->path);
		}
	}
?>