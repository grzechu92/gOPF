<?php 
	namespace System;

	/**
	 * Storage module of framework
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Storage extends Singleton {
		/**
		 * @see \System\Driver::APC
		 */
		const APC = Driver::APC;

		/**
		 * @see \System\Driver::FILESYSTEM
		 */
		const FILESYSTEM = Driver::FILESYSTEM;

		/**
		 * @see \System\Driver::SERIALIZED_FILESYSTEM
		 */
		const SERIALIZED_FILESYSTEM = Driver::SERIALIZED_FILESYSTEM;

		/**
		 * @see \System\Driver::MEMCACHED
		 */
		const MEMCACHED = Driver::MEMCACHED;

		/**
		 * @see \System\Driver::MEMCACHED
		 */
		const DATABASE = Driver::DATABASE;

		/**
		 * Shared container name
		 * @var string
		 */
		const SHARED = '__SHARED';

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
		 * @param string $driver Container driver type
		 * @return \System\Storage\Container Container instance
		 */
		public static function factory($name, $driver = '') {
			if (!self::hasInstance()) {
				self::instance();
			}

			if (empty($driver)) {
				$driver = self::$config->driver;
			}

			if (!isset(self::$containers[$name])) {
				$value = self::SHARED ? new \stdClass() : null;
				self::$containers[$name] = new \System\Storage\Container($name, $value, Driver::factory($driver, 'STORAGE'.$name, 0));
			}

			return self::$containers[$name];
		}
		
		/**
		 * Sets value into shared storage container
		 * 
		 * @param string $name Shared storage field name
		 * @param mixed $value Shared storage field value
		 */
		public static function set($name, $value) {
			$container = self::getSharedContainer();

			$content = $container->get();
			$content->$name = $value;

			$container->set($content);
		}

		/**
		 * Returns value of shared storage container
		 *
		 * @param string $name Shared storage field name
		 * @return mixed Shared storage field value
		 */
		public static function get($name) {
			$content = self::getSharedContainer()->get();

			if (isset($content->$name)) {
				return $content->$name;
			} else {
				return null;
			}
		}

		/**
		 * Removes shared storage field
		 *
		 * @param string $name Shared storage field name
		 */
		public static function delete($name) {
			$container = self::getSharedContainer();

			$content = $container->get();

			if (isset($content->$name)) {
				unset($content->$name);
			}

			$container->set($content);
		}

		/**
		 * Reads value of shared container from driver
		 */
		public static function read() {
			self::getSharedContainer()->read();
		}

		/**
		 * Saves value of shared container into driver
		 */
		public static function write() {
			self::getSharedContainer()->write();
		}

		/**
		 * Returns initialized shared container
		 *
		 * @return \System\Storage\Container Initialized shared container
		 */
		private static function getSharedContainer() {
			return self::factory(self::SHARED);
		}
	}
?>