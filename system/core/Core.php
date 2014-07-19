<?php
	namespace System;

	
	/**
	 * Holds all framework modules in place
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Core extends Container {
		/**
		 * gOPF Core version number
		 * @var string
		 */
		const VERSION = '1.8.3';
		
		/**
		 * gOPF Core build time
		 * @var string
		 */
		const BUILD = '140719044152';
		
		/**
		 * gOPF Core stage (__DEVELOPMENT or __PRODUCTION)
		 * @var int
		 */
		const STAGE = __STAGE;
		
		/**
		 * Core object instance
		 * @var \System\Core
		 */
		public static $instance;
		
		/**
		 * Unique User ID
		 * @var string
		 */
		public static $UUID;
		
		/**
		 * Array with events
		 * @var \System\Core\Event[]
		 */
		private static $events = array();
		
		/**
		 * Creates instance of Core class, and loads UUID (Unique User ID)
		 */
		public function __construct() {
			self::$instance = $this;
			
			self::getUUID();
		}
		
		/**
		 * Returns instance of Core object
		 * 
		 * @return \System\Core Object instance
		 */
		public static function instance() {
			return self::$instance;
		}
		
		/**
		 * Gets UUID from user, if not defined, generates it
		 */
		public static function getUUID() {
			$UUID = isset($_COOKIE['__UUID']) ? $_COOKIE['__UUID'] : false;
			
			if (!$UUID || !preg_match('#([0-9a-f]{40})#', $UUID)) {
				self::generateUUID();
			} else {
				self::$UUID = $UUID;
			}
		}
		
		/**
		 * Generates new UUID
		 */
		public static function generateUUID() {
			self::$UUID = sha1('gOPF-UUID'.(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '').rand(1, 1000000));
			
			setcookie('__UUID', self::$UUID, time()+24*3600, '/');
		}
		
		/**
		 * Adds event to core events registry
		 * 
		 * @param \System\Core\Event $event Event to add
		 */
		public static function addEventListener(\System\Core\Event $event) {
			if (!is_array(self::$events[$event->name])) {
				self::$events[$event->name] = array();
			}
			
			self::$events[$event->name][] = $event;
		}
		
		/**
		 * Calls event from events registry, if exits
		 * 
		 * @param string $name Event name
		 */
		public static function callEvent($name) {
			if (isset(self::$events[$name])) {
				foreach (self::$events[$name] as $event) {
					$event->closure();
				}
			}
		}

		/**
		 * Initializes all important core components
		 */
		public function initialize() {
			$this->i18n = new I18n();
			$this->request = new Request();
			$this->session = new Session();
			$this->user = new User();
			$this->database = new Database();
			$this->router = new Router();
			$this->dispatcher = new Dispatcher();
			$this->cache = new Cache();
			$this->storage = new Storage();
		}
		
		/**
		 * Starts request processing 
		 */
		public function run() {
			$this->dispatcher->dispatch();
		}
	}
?>