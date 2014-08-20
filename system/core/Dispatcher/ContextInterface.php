<?php
	namespace System\Dispatcher;
	
	/**
	 * Interface which describes context of request processing
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	interface ContextInterface {
		/**
		 * Returns controller instance, if it's not loaded, loads it
		 * 
		 * @param string $name Controller name
		 * @return \System\Controller Requested controller
		 */
		public function getController($name);
		
		/**
		 * Returns model instance, if it's not loaded, loads it
		 * 
		 * @param string $name Model name
		 * @return \System\Model Requested model
		 */
		public function getModel($name);
		
		/**
		 * Calls controller action with action existence checking and ACL permission access 
		 * 
		 * @param string $name Controller name
		 * @param string $action Action name
		 * @param bool $dynamic Dynamic call
		 * @return mixed Request result
		 */
		public function callController($name,  $action = 'main', $dynamic = false);
		
		/**
		 * Processes user request in selected request
		 */
		public function process();
	}
?>