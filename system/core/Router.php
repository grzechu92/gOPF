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
		 * Available routes
		 * @var array
		 */
		private $routes;
		
		/**
		 * Router status
		 * @var bool
		 */
		private $status = true;

		/**
		 * Constructor of router module
		 */
		public function __construct() {
            $routes = Config::factory('routes.ini', Config::APPLICATION);

			if (!$routes->enabled) {
				exit();
			}
			
			$routes = $routes->getContent();
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
			
			foreach (array('context', 'controller', 'action') as $variable) {
				Request::$$variable = (isset($matched->values->{$variable})) ? $matched->values->{$variable} : null;
				unset($matched->values->{$variable});
			}
			
			Request::$parameters = new \System\ArrayContainer((array) $matched->values);
		}
	}
?>