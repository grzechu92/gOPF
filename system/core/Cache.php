<?php
	namespace System;
	use System\Cache\Exception;
	use System\Cache\Element;
	
	/**
	 * Cache module of framework
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Cache extends Singleton {
		/**
		 * Element which you want to cache is private (only for one user)
		 * @var int
		 */
		const USER_CACHE = 1;
		
		/**
		 * Element which you want to cache is public (anyone)
		 * @var int
		 */
		const GLOBAL_CACHE = 2;
		
		/**
		 * Holds all loaded cache elements
		 * @var array
		 */
		private static $elements = array(
			self::USER_CACHE => array(),
			self::GLOBAL_CACHE => array()
		);
		
		/**
		 * Loaded status flags of each cache type
		 * @var array
		 */
		private static $loaded = array(
			self::USER_CACHE => false,
			self::GLOBAL_CACHE => false
		);
		
		/**
		 * Cache module drivers
		 * @var array
		 */
		private static $driver = array(
			self::USER_CACHE => null,
			self::GLOBAL_CACHE => null
		);
		
		/**
		 * Save status of cache types
		 * @var array
		 */
		private static $save = array(
			self::USER_CACHE => false,
			self::GLOBAL_CACHE => false
		);

		/**
		 * Cache module configuration
		 * @var \System\Config
		 */
		private $config;
		
		/**
		 * Initiates Cache module and loads user cache data
		 */
		protected function __construct() {
			$this->config = Config::factory('cache.ini', Config::APPLICATION);
			$this->initDrivers();
		}
		
		/**
		 * Cleans and saves cache via selected driver
		 */
		public function __destruct() {
			$this->cleanCache();
			self::save();
		}
		
		/**
		 * Sets value into user cache
		 * 
		 * @param string $name Name of cached value
		 * @param mixed $value Value to cache 
		 * @param int $expires Lifetime of cached value
		 * @param int $type Cache type (Cache::GLOBAL_CACHE or Cache::USER_CACHE)
		 */
		public static function set($name, $value, $expires, $type = self::USER_CACHE) {
			self::isLoaded($type);
			
			self::$elements[$type][$name] = new Element($name, $value, $expires);
			self::$save[$type] = true;
		}
		
		/**
		 * Returns value of cached element if valid
		 *
		 * @param string $name Cached value name
		 * @param int $type Cache type (Cache::GLOBAL_CACHE or Cache::USER_CACHE)
		 * @return mixed Cached value (returns null when cache element is expired)
		 */
		public static function get($name, $type = self::USER_CACHE) {
			self::isLoaded($type);
			
			if (self::isValid($name, $type)) {
				return self::$elements[$type][$name]->value;
			}
			
			return null;
		}
		
		/**
		 * Removes selected cache element
		 * 
		 * @param string $name Cached value name
		 * @param int $type Cache type (Cache::GLOBAL_CACHE or Cache::USER_CACHE)
		 */
		public static function remove($name, $type = self::USER_CACHE) {
			self::isLoaded($type);
			
			if (self::isValid($name, $type)) {
				unset(self::$elements[$type][$name]);
				self::$save[$type] = true;
			}
		}
		
		/**
		 * Checks element if is valid
		 * 
		 * @param string $name Cached value name
		 * @param int $type Cache type (Cache::GLOBAL_CACHE or Cache::USER_CACHE)
		 * @return bool Is valid?
		 */
		public static function isValid($name, $type = self::USER_CACHE) {
			self::isLoaded($type);
			
			if (!empty(self::$elements[$type][$name]) && self::$elements[$type][$name]->expires > time()) {
				return true;
			}
			
			return false;
		}
		
		/**
		 * Checks if cache data is loaded, if not, loads it
		 * 
		 * @param int $type Cache type
		 */
		private static function isLoaded($type) {
            if (!self::hasInstance()) {
                self::instance();
            }

			if (isset(self::$loaded[$type]) && !self::$loaded[$type]) {
				self::load($type);
			}
		}
		
		/**
		 * Loads cache data of selected type
		 * 
		 * @param int $type Cache type
		 */
		private static function load($type) {
			self::$elements[$type] = self::$driver[$type]->get();
			self::$loaded[$type] = true;
		}
		
		/**
		 * Saves user cache data into driver
		 */
		private static function save() {
			foreach (self::$loaded as $type=>$status) {
				if ($status) {
					if (empty(self::$elements[$type])) {
						self::$driver[$type]->remove();
					} else {
						if (self::$save[$type]) {
							self::$driver[$type]->set(self::$elements[$type]);
						}
					}
				}
			}
		}
		
		/**
		* Initiates cache drivers
		*/
		private function initDrivers() {
			$className = '\\System\\Cache\\'.$this->config->driver;
				
			self::$driver[self::USER_CACHE] = new $className(Core::$UUID, $this->config->lifetime);
			self::$driver[self::GLOBAL_CACHE] = new $className('0000000000000000000000000000000000000000', $this->config->lifetime);
		}
		
		/**
		 * Cleans cache from expired elements
		 */
		private function cleanCache() {
			foreach (self::$elements as $type=>$elements) {
				if (!empty($elements)) {
					foreach ($elements as $id=>$element) {
						if ($element->expires < time()) {
							unset(self::$elements[$type][$id]);
						}
					}
				}
			}
		}
	}
?>