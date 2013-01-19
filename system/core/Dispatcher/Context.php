<?php
	namespace System\Dispatcher;
	use System\Request;
	use System\Filesystem;
	use System\I18n;
	use System\Core;
	
	/**
	 * Abstract class of contexts which request might be processed
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	abstract class Context {
		/**
		 * Array of loaded controllers
		 * @var array
		 */
		private $controllers = array();
		
		/**
		 * Array of loaded models
		 * @var array
		 */
		private $models = array();
		
		/**
		 * @see System\Dispatcher.ContextInterface::getController()
		 */
		public function getController($name) {
			if (!in_array($name, $this->controllers)) {
				$this->loadController($name);
			}
			
			return $this->controllers[$name];
		}
		
		/**
		 * @see System\Dispatcher.ContextInterface::callController()
		 */
		public function callController($name, $action = 'main', $dynamic = false) {
			$controller = $this->getController($name);
			
			if ($dynamic) {
				$this->checkState($name);
			}
			
			$this->checkAction($controller, $action);
			$this->checkAccess($name, $action);
			
			return $controller->{$action.'Action'}();
		}
		
		/**
		 * @see System\Dispatcher.ContextInterface::getModel()
		 */
		public function getModel($name) {
			if (!in_array($name, $this->models)) {
				$this->loadModel($name);
			}
				
			return $this->models[$name];
		}
		
		/**
		 * Loads requested controller into registry
		 * 
		 * @param string $name Controller name
		 */
		protected function loadController($name) {
			$this->checkController($name);
			
			$class = '\\Controllers\\'.$name.'Controller';
			$this->controllers[$name] = new $class();
		}
		
		/**
		 * Loads requested model into registry
		 * 
		 * @param string $name Model name
		 */
		protected function loadModel($name) {
			$this->checkModel($name);
			
			$class = '\\Models\\'.$name.'Model';
			$this->models[$name] = new $class(); 
		}
		
		/**
		 * Checks if controller is static or dynamic
		 * 
		 * @param string $name Name of controller
		 * @throws Exception
		 */
		protected function checkState($name) {
			$class = '\\Controllers\\'.$name.'Controller';
			
			if (!$class::$DYNAMIC) {
				throw new Exception(I18n::translate('STATIC_CONTROLLER', array($name)), 404);
			}
		}
		
		/**
		 * Checks controller existence
		 *
		 * @param string $name Name of controller
		 * @throws \System\Core\Exception
		 */
		protected function checkController($name) {
			if (!Filesystem::checkFile(__APPLICATION_PATH.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$name.'Controller.php')) {
				throw new Exception(I18n::translate('CONTROLLER_NOT_FOUND', array($name)), 404);
			}
		}
		
		/**
		 * Checks model existence
		 * 
		 * @param string $name Name of model
		 * @throws \System\Core\Exception
		 */
		protected function checkModel($name) {
			if (!Filesystem::checkFile(__APPLICATION_PATH.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.$name.'Model.php')) {
				throw new Exception(I18n::translate('MODEL_NOT_FOUND', array($name)), 404);
			}
		}
		
		/**
		 * Checks if action exists in controller
		 * 
		 * @param \System\Controller $controller Controller to check
		 * @param string $action Method name
		 * @throws \System\Core\Exception
		 */
		protected function checkAction(\System\Controller $controller, $action = 'main') {
			if (!is_callable(array($controller, $action.'Action'))) {
				throw new Exception(I18n::translate('ACTION_NOT_FOUND', array($action)), 404);
			}
		}
		
		/**
		 * Checks access to requested controller and action
		 */
		protected function checkAccess($name, $action) {
			if (!$this->isAllowed(Request::$controller, Request::$action)) {
				throw new Exception(I18n::translate('ACCESS_DENIED'), 401);
			}
		}
		
		/**
		 * Checks if user has permission to run selected action in requested controller
		 * 
		 * @param string $controller Controller name
		 * @param string $action Action name
		 * @return bool User is allowed to access action
		 */
		protected function isAllowed($controller, $action) {
			$class = '\\Controllers\\'.$controller.'Controller';
			
			$list = $class::$ACL;
			rsort($list);
			
			foreach ($list as $class) {
				if (is_array($class)) {
					$key = key($class);
					
					if ($key == $action) {
						return Core::instance()->user->is($class[$key]);
					}
				} else {
					if (Core::instance()->user->is($class)) {
						return true;
					}
				}
			}
			
			return false;
		}
	}
?>