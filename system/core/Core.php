<?php
	namespace System;

	/**
	 * Holds all framework modules in place
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
     *
     * @property \System\Dispatcher $dispatcher
     * @property \System\Dispatcher\ContextInterface $context
     * @property \System\User $user
	 */
	class Core extends Singleton {
		/**
		 * gOPF Core version number
		 * @var string
		 */
		const VERSION = '2.0.0 alpha';

		/**
		 * gOPF Core build time
		 * @var string
		 */
		const BUILD = '140812120632';

		/**
		 * gOPF Core stage (__DEVELOPMENT or __PRODUCTION)
		 * @var int
		 */
		const STAGE = __STAGE;
		
		/**
		 * Unique User ID
		 * @var string
		 */
		public static $UUID;

        /**
         * Loaded core modules
         * @var object[]
         */
        private $modules = array();
		
		/**
		 * Creates instance of Core class, and loads UUID (Unique User ID)
		 */
		protected function __construct() {
            $UUID = isset($_COOKIE['__UUID']) ? $_COOKIE['__UUID'] : false;

            if (!$UUID || !preg_match('#([0-9a-f]{40})#', $UUID)) {
                self::generateUUID();
            } else {
                self::$UUID = $UUID;
            }
		}

        /**
         * Get core module
         *
         * @param string $name Module name
         * @return object Module
         */
        public function __get($name) {
            if (!isset($this->modules[$name])) {
                $class = '\\System\\'.ucfirst($name);
                $this->modules[$name] = new $class();
            }

            return $this->modules[$name];
        }

        /**
         * Set core module
         *
         * @param string $name Module name
         * @param object $value Module
         */
        public function __set($name, $value) {
            $this->modules[$name] = $value;
        }

        /**
         * Drop client UUID
         */
        public static function dropUUID() {
            self::generateUUID();
        }
		
		/**
		 * Generates new UUID
		 */
		private static function generateUUID() {
			self::$UUID = sha1('gOPF-UUID'.(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '') . rand(1, 1000000));
			
			setcookie('__UUID', self::$UUID, time() + 24 * 3600, '/');
		}
		
		/**
		 * Starts request processing
		 */
		public function run() {
            Request::instance();
            Router::instance();

			$this->dispatcher->dispatch();
		}
	}
?>