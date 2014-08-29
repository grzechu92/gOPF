<?php
	namespace System;
	
	/**
	 * Holds all variables from user
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Request extends Singleton {
		/**
		 * Requested controller
		 * @var string
		 */
		public static $controller;
		
		/**
		 * Requested action
		 * @var string
		 */
		public static $action;

        /**
         * Request language
         * @var string
         */
        public static $language;

        /**
         * Internationalized request ID
         * @var string
         */
        public static $i18n;
		
		/**
		 * Requested URL parameters parsed by router
		 * @var array
		 */
		public static $parameters;
		
		/**
		 * Page context
		 * @var string
		 */
		public static $context;
		
		/**
		* Holds processed content of $_GET
		* @var array
		*/
		public static $get = array();
		
		/**
		 * Holds processed content of $_POST
		 * @var array
		 */
		public static $post = array();
		
		/**
		* Holds processed content of $_COOKIE
		* @var array
		*/
		public static $cookie = array();
		
		/**
		* Holds content of $_FILES
		* @var array
		*/
		public static $files = array();
		
		/**
		 * Holds request parser URL
		 * @var string
		 */
		public static $URL;
		
		/**
		 * CLI arguments array
		 * @var array
		 */
		public static $CLI = array();
		
		/**
		 * Filters global variables content
		 */
		protected function __construct() {
			self::$CLI = isset($_SERVER['argv']) ? $_SERVER['argv'] : array();
			self::$URL = isset(self::$CLI[1]) ? self::$CLI[1] : $this->parseURL();
			
			if (get_magic_quotes_gpc()) {
				$this->removeMagicQuotes();
			} else {
				self::$get = $_GET;
				self::$post = $_POST;
				self::$cookie = $_COOKIE;
			}
			
			self::$files = $_FILES;

			unset($_GET, $_POST, $_COOKIE, $_FILES, $_REQUEST);
		}
		
		/**
		* Redirect request
		*
		* @param string $location Location for redirect
		*/
		public static function redirect($location) {
			header('Location: '.$location);
			exit();
		}
		
		/**
		 * Removes all MagicQuotes trash
		 */
		private function removeMagicQuotes() {
			function process(&$base, $array) {
				foreach ($array as $key=>$value) {
					$key = stripslashes($key);
					
					if (is_array($value)) {
						process($base[$key], $value);
					} else {
						$base[$key] = stripslashes($value);
					}
				}
			};
			
			$variables = array(
				'get' => $_GET,
				'post' => $_POST,
				'cookie' => $_COOKIE
			);
			
			foreach ($variables as $variable=>$content) {
				process(self::$$variable, $content);
			}
		}

        /**
         * Parse request URL
         *
         * @return string Parsed URL
         */
        private function parseURL() {
			$url = explode('?', $_SERVER['REQUEST_URI']);
			
			return substr($url[0], 1);
		}
	}
?>