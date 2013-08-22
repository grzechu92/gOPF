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
				$parser->merge($this->container);
			}
		}
		
		/**
		 * Allows to merge values of two configuration files
		 * 
		 * @param \System\Config\File $file File with values to merge
		 * @param bool $removes Allow to fill with null
		 */
		public function merge(File $file, $removes = false) {
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
		 * Edites or creates value in config array
		 * 
		 * @param string $array Config array name
		 * @param string $offset Config array variable name
		 * @param string $value Config array variable value
		 */
		public function setArrayValue($array, $offset, $value) {
			$content = $this->get($array);
			$content[$offset] = $value;
			$this->set($array, $content);
		}
		
		/**
		 * Returns value of requested variable from selected array in config file
		 * 
		 * @param string $array Config array name
		 * @param string $offset Config array variable name
		 * @return string Config array variable value
		 */
		public function getArrayValue($array, $offset) {
			$content = $this->get($array);
			
			if (empty($content) || !isset($content[$offset])) {
				return null;
			}
			
			return $content[$offset];
		}
		
		/**
		 * Removes selected value
		 * 
		 * @param string $offset Value to remove name
		 */
		public function remove($offset) {
			$this->set($offset, Line::REMOVE);
		}
		
		/**
		 * Removes selected array value
		 * 
		 * @param string $array Array name
		 * @param string $offset Name of value to remove in array
		 */
		public function removeFromArray($array, $offset) {
			$this->setArrayValue($array, $offset, Line::REMOVE);
		}
		
		/**
		 * Returns raw content of configuration file
		 * 
		 * @return array Configuration file content
		 */
		public function getContent() {
			return $this->container;
		}
	}
?>