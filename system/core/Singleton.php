<?php
	namespace System;

	/**
	 * Allows to easily apply singleton in modules
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	abstract class Singleton {
		/**
		 * Singleton object implementation
		 * @var \System\Singleton[]
		 */
		private static $instances = array();

		/**
		 * Return object instance
		 *
		 * @return $this Object instance
		 */
		public static function instance() {
			$class = get_called_class();

			if (!isset(self::$instances[$class])) {
				self::$instances[$class] = new $class();
			}

			return self::$instances[$class];
		}

		/**
		 * Checks if called class has instance?
		 *
		 * @return bool Has instance?
		 */
		protected static function hasInstance() {
			return isset(self::$instances[get_called_class()]);
		}

		/**
		 * Object constructor (protected or private!)
		 */
		protected function __construct() {}

		/**
		 * Prevent from cloning
		 */
		final private function __clone() {}
	}
?>