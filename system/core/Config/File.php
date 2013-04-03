<?php
	namespace System\Config;
	
	/**
	 * Config file class
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class File extends \System\Container {
		/**
		 * Path to configuration file
		 * @var string
		 */
		private $path;
		
		/**
		 * Is file editable?
		 * @var bool
		 */
		private $editable = false;
		
		/**
		 * Has file been edited?
		 * @var bool
		 */
		private $edited = false;
		
		/**
		 * Initiates configuration file
		 * 
		 * @param array $data Configuration file content
		 * @param bool $editable Is editable?
 		 * @param string $path Path to configuration file
		 */
		public function __construct($data, $editable, $path) {
			$this->container = $data;
			$this->editable = $editable;
			$this->path = $path;
		}
		
		/**
		 * If file has been edited, saves changes
		 */
		public function __destruct() {
			if ($this->edited) {
				$parser = new Parser($this->path);
				$parser->process($this->container);
			}
		}
		
		/**
		 * Allows to merge values of two configuration files
		 * 
		 * @param \System\Config\File $file File with values to merge
		 * @param bool $removes Allow to fill with null
		 */
		public function merge(File $file, $removes = false) {
			var_dump(count($file));
			if (count($file) > 0) {
				foreach ($file as $name=>$value) {
					if ($removes || $value != '') {
						$this->set($name, $value);
					}
				}
			}
		}
		
		/**
		 * Checks if file can be edited and edites it
		 * 
		 * @see \System\Container::set()
		 */
		public function set($offset, $value) {
			if ($this->editable) {
				$this->container[$offset] = $value;
				$this->edited = true;
			} else {
				throw new Exception(\System\I18n::translate('CONFIG_EDIT'), $this->path);
			}
		}
		
		/**
		 * Returns raw content of configuration file
		 * @return array Configuration file content
		 */
		public function getContent() {
			return $this->container;
		}
	}
?>