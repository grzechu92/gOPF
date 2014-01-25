<?php
	namespace System\Database;
	
	/**
	 * Database engine interface
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	interface EngineInterface {
		/**
		 * Saves engine config into class
		 * 
		 * @param array $config Engine config
		 */
		public function __construct($config);
		
		/**
		 * Connects to database using selected engine
		 */
		public function connect();
		
		/**
		 * Returns handler to database
		 * 
		 * @return mixed Database handler
		 */
		public function handler();
	}
?>