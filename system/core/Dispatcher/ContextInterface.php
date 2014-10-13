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
		 * Returns controller instance
		 * 
		 * @param string $name Controller name
		 * @return \System\Controller Requested controller
		 */
		public function getController($name);
		
		/**
		 * Returns model instance
		 * 
		 * @param string $name Model name
		 * @return \System\Model Requested model
		 */
		public function getModel($name);

        /**
         * Check if controller and action is accessible to current user
         *
         * @param string $controller Controller name
         * @param string $action Action name in controller
         * @return bool Is accessible
         */
        public function isAccessible($controller, $action);

        /**
         * Checks if controller is dynamic
         *
         * @param string $controller Controller name
         * @return bool Is dynamic
         */
        public function isDynamic($controller);

        /**
         * Call action with parameter binding
         *
         * @param string $controller Controller name
         * @param string $action Action name in controller
         * @return mixed Result
         */
        public function callAction($controller, $action);

		/**
		 * Processes user request in selected request
		 */
		public function process();
	}
?>