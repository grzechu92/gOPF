<?php
	namespace System\Dispatcher;
	use \System\Filesystem;
	use \System\I18n;
	use \System\Core;
    use \System\Request;
    use \System\Loader\Exception as LoaderException;
	
	/**
	 * Abstract class of contexts which request might be processed
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	abstract class Context {
        /**
         * Models namespace
         * @var string
         */
        const MODELS_NAMESPACE = '\\Models\\';

        /**
         * Controllers namespace
         * @var string
         */
        const CONTROLLERS_NAMESPACE = '\\Controllers\\';

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
            $name .= 'Controller';

			if (!isset($this->controllers[$name])) {
				$this->loadController($name);
			}
			
			return $this->controllers[$name];
		}

        /**
         * @see \System\Dispatcher\ContextInterface::getModel()
         */
        final public function getModel($name) {
            $name .= 'Model';

            if (!isset($this->models[$name])) {
                $this->loadModel($name);
            }

            return $this->models[$name];
        }

        /**
         * @see \System\Dispatcher\ContextInterface::isAccessible()
         */
        final public function isAccessible($controller, $action) {
            $reflection = $this->getReflection(self::CONTROLLERS_NAMESPACE.$controller.'Controller');
            $annotation = new Annotation($reflection->getMethod($action.'Action')->getDocComment());

            $acl = $annotation->get(Annotation::ACL);

            if ($annotation->get(Annotation::STATE == 'static')) {
                throw new Exception(I18n::translate('STATIC_CONTROLLER', array($controller)), 404);
            }

            if (!$acl) {
                return true;
            } else {
                foreach (explode(' ', $acl) as $level) {
                    if (Core::instance()->user->is($level)) {
                        return true;
                    }
                }

                throw new Exception(I18n::translate('ACCESS_DENIED'), 401);
            }
        }
		
        /**
         * @see \System\Dispatcher\ContextInterface::callAction()
         */
        final public function callAction($controller, $action) {
            $action .= 'Action';

            $reflection = $this->getReflection(self::CONTROLLERS_NAMESPACE.$controller.'Controller');
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

                    if (!$found) {
                        if (!$parameter->isOptional()) {
                            throw new Exception(I18n::translate('ACTION_NOT_FOUND', array($action)), 404);
                        } else {
                            $params[] = null;
                        }
                    }
                }
            }

            return call_user_func_array(array($this->getController($controller), $action), $params);
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
                $class = self::MODELS_NAMESPACE.$name;
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
                $class = self::CONTROLLERS_NAMESPACE.$name;
                $this->controllers[$name] = new $class();
            } catch (LoaderException $e) {
                throw new Exception(I18n::translate('CONTROLLER_NOT_FOUND', array($name)), 404);
            }
        }
    }
?>