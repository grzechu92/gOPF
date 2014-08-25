<?php
	namespace System\Driver;
    use \stdClass;
	
	/**
	 * Diver based on PHP Sessions
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0> 
	 */
	class Session implements DriverInterface {
		/**
		 * Session lifetime
		 * @var int
		 */
		public $lifetime = 86400;
		
		/**
	 	 * Container name in session
		 * @var string
		 */
		protected $name;
		
		/**
		 * @see \System\Drivers\DriverInterface::__construct()
		 */
		public function __construct($id, $lifetime = 0) {
			$this->name = $id;
			
			ini_set('session.gc_maxlifetime', $lifetime);
			
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
			
			if (empty($_SESSION)) {
				$_SESSION = new stdClass();
			}

            if (!isset($_SESSION->$id)) {
                $_SESSION->$id = new stdClass();
            }
		}
		
		/**
		 * @see \System\Drivers\DriverInterface::set()
		 */
		public function set($content) {
			$_SESSION->{$this->name} = $content;
		}
		
		/**
		 * @see \System\Drivers\DriverInterface::get()
		 */
		public function get() {
			return $_SESSION->{$this->name};
		}
		
		/**
		 * @see \System\Drivers\DriverInterface::remove()
		 */
		public function remove() {
			if (session_status() == PHP_SESSION_ACTIVE) {
				session_destroy();
			}
		}
		
		/**
		 * @see \System\Drivers\DriverInterface::clear()
		 */
		public function clear() {
			throw new \System\Core\Exception(\System\I18n::translate('UNSUPPORTED_DRIVER_METHOD', array(__CLASS__, 'clear()')));
		}
	}
?>