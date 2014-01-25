<?php
	namespace System\Terminal;
	
	/**
	 * Terminal command interface
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	interface CommandInterface {
		/**
		 * Executes command logic
		 */
		public function execute();
		
		/**
		 * Displays command help
		 * 
		 * @return \System\Terminal\Help Help content
		 */
		public function help();
		
		/**
		 * Action when command is installed
		 */
		public function onInstall();
		
		/**
		 * Action when command is uninstalled
		 */
		public function onUninstall();
	}
?>