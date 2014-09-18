<?php
	namespace System;
    use \System\Cache\Container as CacheContainer;

	/**
	 * Cache module of framework
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Cache extends Singleton {
        /**
         * @see \System\Cache\Container::USER
         */
		const USER = CacheContainer::USER;

        /**
         * @see \System\Cache\Container::COMMON
         */
		const COMMON = CacheContainer::COMMON;

        /**
         * @see \System\Cache\Container::RUNTIME
         */
        const RUNTIME = CacheContainer::RUNTIME;
		
		/**
		 * Loaded cache containers
		 * @var \System\Cache\Container[]
		 */
        private static $containers = array();

		/**
		 * Cache module configuration
		 * @var \System\Config
		 */
		private static $config;
		
		/**
		 * Initiates Cache module and loads user cache data
		 */
		protected function __construct() {
			self::$config = Config::factory('cache.ini', Config::APPLICATION);
		}
		
		/**
		 * Sets value into user cache
		 * 
		 * @param string $name Name of cached value
		 * @param mixed $value Value to cache 
		 * @param int $expires Lifetime of cached value
		 * @param int $type Cache type (Cache::COMMON, Cache::USER, Cache::RUNTIME)
		 */
		public static function set($name, $value, $expires, $type = self::USER) {
			self::isLoaded($type);

			self::$containers[$type]->set($name, $value, $expires);
		}
		
		/**
		 * Returns value of cached element if valid
		 *
		 * @param string $name Cached value name
		 * @param int $type Cache type (Cache::COMMON, Cache::USER, Cache::RUNTIME)
		 * @return mixed Cached value (returns null when cache element is expired)
		 */
		public static function get($name, $type = self::USER) {
			self::isLoaded($type);
			
			return self::$containers[$type]->get($name);
		}
		
		/**
		 * Removes selected cache element
		 * 
		 * @param string $name Cached value name
		 * @param int $type Cache type (Cache::COMMON, Cache::USER, Cache::RUNTIME)
		 */
		public static function remove($name, $type = self::USER) {
			self::isLoaded($type);
			
			self::$containers[$type]->remove($name);
		}

        /**
         * Clear cache container
         *
         * @param int $type Cache type (Cache::COMMON, Cache::USER, Cache::RUNTIME)
         */
        public static function clear($type = self::USER) {
            self::isLoaded($type);

            self::$containers[$type]->clear();
        }
		
		/**
		 * Checks element if is valid
		 * 
		 * @param string $name Cached value name
		 * @param int $type Cache type (Cache::COMMON, Cache::USER, Cache::RUNTIME)
		 * @return bool Is valid?
		 */
		public static function isValid($name, $type = self::USER) {
            self::isLoaded($type);

            return self::$containers[$type]->isValid($name);
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

            if (!isset(self::$containers[$type])) {
                self::$containers[$type] = new CacheContainer($type, self::$config);
            }
		}
	}
?>