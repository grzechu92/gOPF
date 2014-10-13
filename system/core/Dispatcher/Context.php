<?php
	namespace System\Dispatcher;
	use \System\Filesystem;
	use \System\I18n;
	use \System\Core;
    use \System\Request;
    use \ReflectionClass;
    use \System\Loader\Exception as LoaderException;
	
	/**
	 * Abstract class of contexts which request might be processed
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	abstract class Context {
		/**
		 * Array of loaded controllers
		 * @var \System\Controller[]
		 */
		private $controllers = array();
		
		/**
		 * Array of loaded models
		 * @var \System\Model[]
		 */
		private $models = array();

        /**
         * Array of reflections
         * @var \ReflectionClass[]
         */
        private $reflections = array();
		
		/**
		 * @see \System\Dispatcher\ContextInterface::getController()
		 */
		final public function getController($name) {
			if (!isset($this->controllers[$name])) {
				$this->loadController($name);
			}
			
			return $this->controllers[$name];
		}

        /**
         * @see \System\Dispatcher\ContextInterface::getModel()
         */
        final public function getModel($name) {
            if (!isset($this->models[$name])) {
                $this->loadModel($name);
            }

            return $this->models[$name];
        }

        /**
         * @see \System\Dispatcher\ContextInterface::isAccessible()
         */
        final public function isAccessible($controller, $action) {
            $reflection = $this->getReflection($controller);
            $annotation = $reflection->getDocComment();
        }
		
        /**
         * @see \System\Dispatcher\ContextInterface::callAction()
         */
        final public function callAction($controller, $action) {
            $reflection = $this->getReflection($controller);
            $params = array();

            if (!$reflection->hasMethod($action)) {
                throw new Exception(I18n::translate('ACTION_NOT_FOUND', array($action)), 404);
            }

            $method = $reflection->getMethod($action);
            $parameters = $method->getParameters();

            if (!empty($parameters)) {
                foreach ($parameters as $parameter) {
                    $found = false;

                    foreach (array(Request::$parameters, Request::$post, Request::$get) as $array) {
                        if (isset($array[$parameter->name])) {
                            $params[] = $array[$parameter->name];
                            $found = true;
                            break;
                        }
                    }

                    if (!$found && !$parameter->isOptional()) {
                        throw new Exception(I18n::translate('ACTION_NOT_FOUND', array($action)), 404);
                    }

                    $params[] = null;
                }
            }

            return call_user_func_array(array($controller, $action), $params);
        }

        /**
		 * Checks if controller is static or dynamic
		 *
		 * @param string $name Name of controller
		 * @throws \System\Dispatcher\Exception
		 */
		final protected function checkState($name) {
			$class = '\\Controllers\\'.$name.'Controller';

			if (!$class::$DYNAMIC) {
				throw new Exception(I18n::translate('STATIC_CONTROLLER', array($name)), 404);
			}
		}

        /**
		 * Checks access to requested controller and action
		 *
         * @param string $controller Controller name
         * @param string $action Action name
		 * @throws \System\Dispatcher\Exception
		 */
		final protected function checkAccess($controller, $action) {
			if (!$this->isAllowed($controller, $action)) {
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
		final protected function isAllowed($controller, $action) {
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

        /**
		 * Prints JSON formatted data
		 *
		 * @param array|\stdClass Data
		 */
		final protected function toJSON($data) {
			echo json_encode($data);
		}

        /**
         * Return reflection instance
         *
         * @param string $class Class name
         * @return \ReflectionClass Reflection of class name
         */
        final private function getReflection($class) {
            if (isset($this->reflections[$class])) {
                return $this->reflections[$class];
            } else {
                return $this->reflections[$class] = new \ReflectionClass($class);
            }
        }

        /**
         * Loads requested model into registry
         *
         * @param string $name Model name
         * @throws \System\Dispatcher\Exception
         */
        final private function loadModel($name) {
            try {
                $class = '\\Models\\'.$name.'Model';
                $this->models[$name] = new $class();
            } catch (LoaderException $e) {
                throw new Exception(I18n::translate('MODEL_NOT_FOUND', array($name)), 404);
            }
        }

        /**
         * Loads requested controller into registry
         *
         * @param string $name Controller name
         * @throws \System\Dispatcher\Exception
         */
        final private function loadController($name) {
            try {
                $class = '\\Controllers\\'.$name.'Controller';
                $this->controllers[$name] = new $class();
            } catch (LoaderException $e) {
                throw new Exception(I18n::translate('CONTROLLER_NOT_FOUND', array($name)), 404);
            }
        }
    }
?>