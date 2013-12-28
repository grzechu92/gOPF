<?php
	namespace System;
	use \System\Router\Route;
	
	/**
	 * Matches request to matching bootstrap, and runns request processing procedure
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Router {
		/**
		 * Availiable routes
		 * @var array
		 */
		private $routes;
		
		/**
		 * Router status
		 * @var bool
		 */
		private $status = true;

		/**
		 * Contructor of router module
		 */
		public function __construct() {
			$router = Config::factory('router.ini', Config::SYSTEM);

			if (!$router->enabled) {
				exit();
			}
			
			$routes = Config::factory('routes.ini', Config::APPLICATION);
			$routes = $routes->getContent();
			
			$this->match($routes['routes'], $routes['default']);
		}
		
		/**
		 * Matches route for requested URL
		 * 
		 * @param array $routes Availiable routes
		 * @param string $default Default route, selected when no availiable routes is matched
		 */
		private function match($routes, $default) {
			$matched = false;
			
			foreach ($routes as $rule => $values) {
				$rule = new Route($rule, $values);
				
				if (!$rule->match()) {
					continue;
				}
				
				$rule->parse();
				
				 
				
				$matched = $rule;
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