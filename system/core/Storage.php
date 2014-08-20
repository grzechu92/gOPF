<?php 
	namespace System;

	/**
	 * Storage module of framework
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Storage extends Singleton {
        /**
         * APC driver
         * @var string
         */
        const APC = 'APCDriver';

        /**
         * Default driver
         * @var string
         */
        const SESSION = 'DefaultDriver';

        /**
         * Filesystem driver
         * @var string
         */
        const FILESYSTEM = 'FilesystemDriver';

        /**
         * Serialized filesystem driver
         * @var string
         */
        const SERIALIZED_FILESYSTEM = 'SerialziedFilesystemDriver';

		/**
		 * Storage containers
		 * @var \System\Storage\Container[]
		 */
		private static $containers = array();
		
		/**
		 * Storage module configuration
		 * @var \System\Config
		 */
		private static $config;
		
		/**
		 * Initiates storage module, loads configuration file
		 */
		public function __construct() {
			self::$config = Config::factory('storage.ini', Config::APPLICATION);
		}

        /**
         * Returns custom parameters container
         *
         * @param string $name Container name
         * @param string $type Container driver type
         * @return \System\Storage\Container Container instance
         */
        public static function factory($name, $type) {
            return self::initializeContainer($name, $type);
        }
		
		/**
		 * Sets value into storage container
		 * 
		 * @param string $name Storage container name
		 * @param mixed $value Storage container value
		 */
		public static function set($name, $value) {
			self::initializeContainer($name);
			
			self::$containers[$name]->set($value);
		}
		
		/**
		 * Returns value of storage container
		 * 
		 * @param string $name Storage container name
		 * @return mixed Storage container value
		 */
		public static function get($name) {
			self::initializeContainer($name);
			
			return self::$containers[$name]->get();
		}
		
		/**
		 * Removes storage container
		 * 
		 * @param string $name Storage container name
		 */
		public static function delete($name) {
			if (!empty(self::$containers[$name])) {
				self::$containers[$name]->remove();

				unset(self::$containers[$name]);
			}
		}
		
		/**
		 * Reads value of container from driver
		 * 
		 * @param string $name Storage container name
		 */
		public static function read($name) {
			self::initializeContainer($name);
			
			self::$containers[$name]->read();
		}
		
		/**
		 * Saves value of container into driver
		 * 
		 * @param string $name Storage container name
		 */
		public static function write($name) {
			self::$containers[$name]->write();
		}
		
		/**
		 * Marks storage container, as temporary
		 * 
		 * @param string $name Storage container name
		 * @param bool $temp Is temporary?
		 */
		public static function temporary($name, $temp = true) {
			self::initializeContainer($name);
			
			self::$containers[$name]->temporary = $temp;
		}

        /**
         * Initiates storage container with specified name and driver
         *
         * @param string $name Storage name
         * @param string $driver Driver name
         * @return \System\Storage\Container Storage element
         */
		private static function initializeContainer($name, $driver = '') {
            if (!self::hasInstance()) {
                self::instance();
            }

            if (empty($driver)) {
                $driver = self::$config->driver;
            }

			if (!isset(self::$containers[$name])) {
				$driver = '\\System\\Storage\\'.$driver;
				
				return self::$containers[$name] = new \System\Storage\Container($name, null, new $driver(sha1($name), 0));
			} else {
                return self::$containers[$name];
            }
		}
	}
?>