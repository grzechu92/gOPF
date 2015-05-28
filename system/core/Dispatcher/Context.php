<?php

namespace System\Dispatcher;

use System\I18n;
use System\Core;
use System\Request;
use System\Loader\Exception as LoaderException;

/**
 * Abstract class of contexts which request might be processed.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
abstract class Context
{
    /**
     * Repositories namespace.
     *
     * @var string
     */
    const REPOSITORIES_NAMESPACE = '\\Repositories\\';

    /**
     * Controllers namespace.
     *
     * @var string
     */
    const CONTROLLERS_NAMESPACE = '\\Controllers\\';

    /**
     * Array of loaded controllers.
     *
     * @var \System\Controller[]
     */
    private $controllers = array();

    /**
     * Array of loaded repositories.
     *
     * @var \System\Repository[]
     */
    private $repositories = array();

    /**
     * Array of reflections.
     *
     * @var \ReflectionClass[]
     */
    private $reflections = array();

    /**
     * @see \System\Dispatcher\ContextInterface::getController()
     */
    final public function getController($name)
    {
        if (!isset($this->controllers[$name])) {
            $this->loadController($name);
        }

        return $this->controllers[$name];
    }

    /**
     * @see \System\Dispatcher\ContextInterface::getRepository()
     */
    final public function getRepository($name)
    {
        if (!isset($this->repositories[$name])) {
            $this->loadRepository($name);
        }

        return $this->repositories[$name];
    }

    /**
     * @see \System\Dispatcher\ContextInterface::isAccessible()
     */
    final public function isAccessible($controller, $action)
    {
        $annotation = $this->getAnnotation($controller, $action);
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
     * @see \System\Dispatcher\ContextInterface::isRestAware()
     */
    final public function isRestAware($controller, $action)
    {
        return $this->getAnnotation($controller, $action)->get(Annotation::REST);
    }

    /**
     * @see \System\Dispatcher\ContextInterface::callAction()
     */
    final public function callAction($controller, $action)
    {
        $reflection = $this->getReflection(self::CONTROLLERS_NAMESPACE . $controller);
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
     * Prints JSON formatted data.
     *
     * @param array|\stdClass Data
     */
    final protected function toJSON($data)
    {
        echo json_encode($data);
    }

    /**
     * Returns annotations from method in controller.
     *
     * @param string $controller Controller name
     * @param string $action     Action name
     *
     * @return \System\Dispatcher\Annotation
     */
    final protected function getAnnotation($controller, $action)
    {
        $reflection = $this->getReflection(self::CONTROLLERS_NAMESPACE . $controller);

        return new Annotation($reflection->getMethod($action)->getDocComment());
    }

    /**
     * Return reflection instance.
     *
     * @param string $class Class name
     *
     * @return \ReflectionClass Reflection of class name
     */
    final private function getReflection($class)
    {
        if (isset($this->reflections[$class])) {
            return $this->reflections[$class];
        } else {
            return $this->reflections[$class] = new \ReflectionClass($class);
        }
    }

    /**
     * Loads requested repository into registry.
     *
     * @param string $name Repository name
     *
     * @throws \System\Dispatcher\Exception
     */
    final private function loadRepository($name)
    {
        try {
            $class = self::REPOSITORIES_NAMESPACE . $name;
            $this->repositories[$name] = new $class();
        } catch (LoaderException $e) {
            throw new Exception(I18n::translate('REPOSITORY_NOT_FOUND', array($name)), 404);
        }
    }

    /**
     * Loads requested controller into registry.
     *
     * @param string $name Controller name
     *
     * @throws \System\Dispatcher\Exception
     */
    final private function loadController($name)
    {
        try {
            $class = self::CONTROLLERS_NAMESPACE . $name;
            $this->controllers[$name] = new $class();
        } catch (LoaderException $e) {
            throw new Exception(I18n::translate('CONTROLLER_NOT_FOUND', array($name)), 404);
        }
    }
}
