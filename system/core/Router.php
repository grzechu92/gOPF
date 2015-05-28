<?php

namespace System;

use System\Router\Route;
use System\Router\Exception;

/**
 * Matches request to matching bootstrap, and starts request processing procedure.
 *
 * @author    Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
 * @license   The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
 */
class Router extends Singleton
{
    /**
     * Router configuration.
     *
     * @var \System\Config
     */
    private $config;

    /**
     * Internationalization module.
     *
     * @var \System\I18n
     */
    private $i18n;

    /**
     * Constructor of router module.
     */
    protected function __construct()
    {
        $this->config = Config::factory('routes.ini', Config::APPLICATION);
        $this->i18n = I18n::instance();

        if (!$this->config->enabled) {
            exit();
        }

        $this->match();
    }

    /**
     * Generate translated URL for specified controller and action.
     *
     * @param string $controller Controller name
     * @param string $action     Controller action
     *
     * @throws \System\Router\Exception
     *
     * @return string Generated translated URL
     */
    public static function generate($controller, $action = 'main')
    {
        $urls = array_flip(self::instance()->config->{'i18n:' . Request::$language});
        $target = $controller . ':' . $action;

        if (isset($urls[$target])) {
            return $urls[$target];
        } else {
            throw new Exception(I18n::translate('ROUTE_I18N_NOT_FOUND', array($target)));
        }
    }

    /**
     * Matches route for requested URL.
     */
    private function match()
    {
        $route = $this->findRoute($this->config->routes);

        if (isset($route->values->language)) {
            $this->i18n->set($route->values->language);
        } else {
            $route->values->language = $this->i18n->selected()->application;
        }

        if (isset($route->values->i18n)) {
            $i18n = $this->config->getContent()['i18n:' . $this->i18n->selected()->application];
            $route->translate($i18n);
        }

        foreach (array('i18n', 'language', 'context', 'controller', 'action') as $variable) {
            Request::$$variable = (isset($route->values->{$variable})) ? $route->values->{$variable} : null;
            unset($route->values->{$variable});

            if ($variable == 'controller') {
                Request::$$variable = ucfirst(Request::$$variable);
            }
        }

        Request::$parameters = new \System\ArrayContainer((array)$route->values);
    }

    /**
     * Find route matching URL request.
     *
     * @param array $routes Available routes
     *
     * @throws \System\Router\Exception
     *
     * @return \System\Router\Route Matching route
     */
    private function findRoute($routes = array())
    {
        $found = null;

        if (count($routes) > 0) {
            foreach ($routes as $rule => $values) {
                $rule = new Route($rule, $values);

                if (!$rule->match()) {
                    continue;
                }

                $rule->parse();

                if (isset($rule->values->validate)) {
                    foreach ($rule->values->validate as $controller) {
                        $class = '\\Controllers\\' . trim($controller) . 'Controller';

                        if (!in_array('System\Router\ValidableInterface', class_implements($class))) {
                            throw new Exception(I18n::translate('ROUTE_NOT_VALIDABLE', array($class)));
                        }

                        /** @var $class \System\Router\ValidableInterface */
                        if ($class::validate($rule)) {
                            $found = null;
                            break;
                        }
                    }
                } else {
                    $found = $rule;
                }

                break;
            }
        }

        if (!($found instanceof Route)) {
            $found = new Route('', $this->config->default);
            $found->parse();
        }

        return $found;
    }
}
