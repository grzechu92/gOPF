<?php
	namespace System;
	use \System\Router\Route;
		
	/**
	 * Matches request to matching bootstrap, and starts request processing procedure
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Router {
        /**
         * Router configuration
         * @var \System\Config
         */
        private $config;

        /**
         * Internationalization module
         * @var \System\I18n
         */
        private $i18n;

		/**
		 * Constructor of router module
		 */
		public function __construct() {
            $this->config = Config::factory('routes.ini', Config::APPLICATION);
            $this->i18n = \System\Core::instance()->i18n;

			if (!$this->config->enabled) {
				exit();
			}
			
			$routes = $this->config->getContent();

			$this->match($routes['routes'], $routes['default']);
		}

		/**
		 * Matches route for requested URL
		 *
		 * @param array $routes Available routes
		 * @param string $default Default route, selected when no available routes is matched
		 */
		private function match($routes, $default) {
			$matched = false;
			
			foreach ($routes as $rule => $values) {
				$rule = new Route($rule, $values);
				
				if (!$rule->match()) {
					continue;
				}
				
				$rule->parse();
				
				if (isset($rule->values->validate)) {
					foreach ($rule->values->validate as $controller) {
						$class = '\\Controllers\\'.trim($controller).'Controller';
												
						if (!in_array('System\Router\ValidableInterface', class_implements($class))) {
							throw new \System\Router\Exception(\System\I18n::translate('ROUTE_NOT_VALIDABLE', array($class)));
						}

                        /** @var $class \System\Router\ValidableInterface */
                        if ($class::validate($rule)) {
							$matched = $rule;
							break;
						}
					}
				} else {
					$matched = $rule;
				}
				
				break;
			}
			
			if (!($matched instanceof Route)) {
				$matched = new Route('', $default);
				$matched->parse();
			}

            if (isset($matched->values->language)) {
                $this->i18n->set($matched->values->language);
            } else {
                $matched->values->language = $this->i18n->selected()->application;
            }

            if (isset($matched->values->i18n)) {
                $i18n = $this->config->getContent()['i18n:'.$this->i18n->selected()->application];
                $matched->translate($i18n);
            }

			foreach (array('i18n', 'language', 'context', 'controller', 'action') as $variable) {
				Request::$$variable = (isset($matched->values->{$variable})) ? $matched->values->{$variable} : null;
				unset($matched->values->{$variable});
			}

			Request::$parameters = new \System\ArrayContainer((array) $matched->values);
		}
	}
?>