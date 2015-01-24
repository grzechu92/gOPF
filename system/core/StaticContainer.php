<?php
	namespace System;

	/**
	 * Static container allows to call class witch ::set() and ::get() methods
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0> 
	 */
	class StaticContainer {
		/**
		 * Holds content of container
		 * @var array
		 */
		protected static $container = array();
		
		/**
		 * Sets variable value in container
		 *
		 * @param string $offset Variable name
		 * @param mixed $value Variable value
		 */
		public static function set($offset, $value) {
			self::$container[$offset] = $value;
		}
		
		/**
		 * Gets variable value from container
		 *
		 * @param string $offset Variable name
		 * @return mixed Variable value
		 */
		public static function get($offset) {
			return self::$container[$offset];
		}
	}
?>