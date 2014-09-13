<?php
	namespace System\Driver;
	
	/**
	 * Interface which describes how to write drivers for framework modules
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	interface DriverInterface {
        /**
         * Saves data into driver
         *
         * @param string $name Driver container name
         * @param int $lifetime Driver container lifetime
         * @param bool $user Driver content depends on user?
         */
		public function __construct($name, $lifetime = 0, $user = false);
		
		/**
		 * Saves data under selected ID by driver
		 * 
		 * @param mixed $content Data to save
		 */
		public function set($content);
		
		/**
		 * Reads data from selected ID by driver
		 * 
		 * @return mixed Data from selected ID
		 */
		public function get();
		
		/**
		 * Removes selected ID from driver database
		 */
		public function remove();
		
		/**
		 * Clears all ID's from selected driver
		 */
		public function clear();
	}
?>