<?php
	namespace System;
	
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
			
			$this->matchRoute($routes['routes'], $routes['default']);
		}
		
		/**
		 * Matches route for requested URL
		 * 
		 * @param array $routes Availiable routes
		 * @param string $defaultRoute Default route, selected when no availiable routes is matched
		 */
		private function matchRoute($routes, $defaultRoute) {
			$route = array();
			$section = '/\<(\w+)\:(numeric|alpha|alphanumeric|any)\>/';
			
			foreach ($routes as $rule=>$defaults) {
				$rule = str_replace('/', '\\/', $rule);
				
				$pattern = preg_replace(
					array('/@alphanumeric/', '/@numeric/', '/@alpha/', '/@any/'),
					array('(\w+)', '(\d+)', '(\D+)', '(.+)'),
					preg_replace($section, '@$2', $rule)
				).'(|\/(.*))';
				
				if (!preg_match('/^'.$pattern.'$/', Request::$URL)) {
					continue;
				}
								
				$route = $this->parseDefaults($defaults);
				
				$names = array();
				preg_match_all($section, $rule, $names);
				
				$values = array();
				preg_match_all('/'.preg_replace($section, '(?P<$1>[^\/]+)', $rule).'/', Request::$URL, $values, PREG_SET_ORDER);
				
				foreach ($names[1] as $name) {
					$route[$name] = $values[0][$name];
				}
				
				break;
			}
			
			if (empty($route)) {
				$route = $this->parseDefaults($defaultRoute);
			}
			
			foreach (array('context', 'controller', 'action') as $variable) {
				Request::$$variable = (isset($route[$variable])) ? $route[$variable] : null;
				unset($route[$variable]);
			}
			
			Request::$parameters = $route;
		}
		
		/**
		 * Parses defaults string from config file
		 * 
		 * @param string $defaults Defaults string
		 * @return array Parsed defaults
		 */
		private function parseDefaults($defaults) {
			$values = array();
			$exploded = explode(',', $defaults);
			
			foreach ($exploded as $part) {
				$separated = explode(':', $part);
				$values[$separated[0]] = $separated[1];
			}
			
			return $values;
		}
	}
?>