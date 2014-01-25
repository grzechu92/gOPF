<?php 
	namespace System;
	use System\Storage\Element;
	
	/**
	 * Storage module of framework
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Storage {
		/**
		 * Storage elements container
		 * @var array
		 */
		private static $elements = array();
		
		/**
		 * Storage module configuration
		 * @var \System\Config
		 */
		private static $config;
		
		/**
		 * Initiates storage module, loads configuration file
		 */
		public function __construct() {
			self::$config = Config::factory('storage.ini', Config::SYSTEM);
		}
		
		/**
		 * Sets value into storage
		 * 
		 * @param string $name Storage name
		 * @param mixed $value Storage value
		 */
		public static function set($name, $value) {
			self::initElement($name);
			
			self::$elements[$name]->set($value);
		}
		
		/**
		 * Returns value of storage element 
		 * 
		 * @param string $name Storage name
		 * @return mixed Storage value
		 */
		public static function get($name) {
			self::initElement($name);
			
			return self::$elements[$name]->get();
		}
		
		/**
		 * Removes storage element driver
		 * 
		 * @param string $name Storage name
		 */
		public static function delete($name) {
			if (!empty(self::$elements[$name])) {
				self::$elements[$name]->remove();
				unset(self::$elements[$name]);
			}
		}
		
		/**
		 * Reads value of storage from driver
		 * 
		 * @param string $name Storage name
		 */
		public static function read($name) {
			self::initElement($name);
			
			self::$elements[$name]->read();
		}
		
		/**
		 * Saves value of storage into driver
		 * 
		 * @param string $name Storage name
		 */
		public static function write($name) {
			self::$elements[$name]->write();
		}
		
		/**
		 * Marks storage element, as temporary
		 * 
		 * @param string $name Storage name
		 * @param bool $temp Is temporary?
		 */
		public static function temporary($name, $temp = true) {
			self::initElement($name);
			
			self::$elements[$name]->temporary = $temp;
		}
		
		/**
		 * Initiates driver of storage element with specified name
		 * 
		 * @param string $name Storage name
		 */
		private static function initElement($name) {
			if (!isset(self::$elements[$name])) {
				$driver = '\\System\\Storage\\'.self::$config->driver;
				
				self::$elements[$name] = new Element($name, null, new $driver(sha1($name), 0));
			}
		}
	}
?>