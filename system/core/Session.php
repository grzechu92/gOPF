<?php
	namespace System;
	use System\Session\Element;
	use System\Core\Exception;
	
	/**
	 * Session module of framework
	 * 
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2014, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Session extends Singleton {
		/**
		 * Session synchronization status
		 * @var bool
		 */
		public $sync = false;
		
		/**
		 * Session data store driver
		 * @var \System\Driver\Driver
		 */
		private $driver;
		
		/**
		 * Session configuration data
		 * @var \System\Config
		 */
		private $config;
		/**
		 * Session data container
		 * @var array
		 */
		private $data = array();
		
		/**
		 * Session protect key
		 * @var string
		 */
		private $protected;
		
		/**
		 * Initiates session module
		 */
		protected function __construct() {
			$this->config = Config::factory('session.ini', Config::APPLICATION);
			
			$this->driver = Driver::factory($this->config->driver, 'SESSION', $this->config->lifetime, true);
			
			$this->load();
			$this->initialize();
			
			if ($this->config->protected) {
				$this->protectSession();
			}
			
			$this->protected = $this->data['__PROTECTED'];
		}
			
		/**
		 * If session synchronization is enabled, sync it on destruct, otherwise save it normally. If session is empty, don't save it, and remove driver
		 */
		public function __destruct() {
			$this->cleanSession();

			if (count($this->data['__ELEMENTS']) > 0 || $this->data['__COUNT'] !== count($this->data['__ELEMENTS'])) {
				if ($this->sync) {
					$this->synchronize();
				} else {
					$this->save();
				}
			} else {
				$this->driver->remove();
			}
		}

		/**
		 * Returns session element value
		 * 
		 * @param string $name Session element name
		 * @return mixed Element value (returns null when session element is expired)
		 */
		public static function get($name) {
			if (isset(self::instance()->data['__ELEMENTS'][$name])) {
				$element = self::instance()->data['__ELEMENTS'][$name];
				
				if ($element->expires === 0 || $element->expires > time()) {
					return $element->value;
				} else {
					return null;
				}
			}
		}
		
		/**
		 * Sets session element value
		 * 
		 * @param string $name Element name
		 * @param mixed $value Element value
		 * @param int $lifetime Element lifetime (optional)
		 */
		public static function set($name, $value, $lifetime = 0) {
			self::instance()->data['__ELEMENTS'][$name] = new Element($name, $value, $lifetime);
		}
		
		/**
		 * Removes selected session element
		 * 
		 * @param string $name Element name
		 */
		public static function remove($name) {
			unset(self::instance()->data['__ELEMENTS'][$name]);
		}
		
		/**
		 * Clears container content
		 */
		public static function clear() {
			self::instance()->data['__ELEMENTS'] = array();
		}
		
		/**
		 * Prints current container content, like print_r()
		 */
		public static function debug($return = false) {
			return print_r(self::instance()->data['__ELEMENTS'], $return);
		}
		
		/**
		 * Synchronizes session elements between opened session
		 */
		public static function synchronize() {
			$session = self::instance();
            $session->sync = true;

			$data = array();

			$data['before'] = isset($session->data['__ELEMENTS']) ? $session->data['__ELEMENTS'] : array();
			$session->load();
			
			$data['after'] = isset($session->data['__ELEMENTS']) ? $session->data['__ELEMENTS'] : array();
			
			$synchronized = array_merge($data['before'], $data['after']);
			
			foreach ($data['before'] as $name=>$before) {
				if (isset($data['after'][$name]) && $data['after'][$name] instanceof Element) {
					$after = $data['after'][$name];
					
					if ($after->modified >= $before->modified) {
						$synchronized[$name] = $after;
					} else {
						$synchronized[$name] = $before;
					}
				}
			}
			
			$session->data['__ELEMENTS'] = $synchronized;
			$session->data['__COUNT'] = count($synchronized);
			$session->data['__PROTECTED'] = $session->protected;
			
			$session->cleanSession();
			$session->save();
		}
		
		/**
		 * Returns array with session elements names
		 * 
		 * @return array Array with elements
		 */
		public static function getElements() {
			$elements = self::instance()->data['__ELEMENTS'];
			
			if (count($elements) > 0) {
				$return = array();
				
				foreach ($elements as $element) {
					$return[] = $element->name;
				}
				
				return $return;
			}
			
			return array();
		}
		
		/**
		 * Extends lifetime of session element
		 *
		 * @param string $name Element name
		 * @param int $lifetime Element lifetime
		 */
		public static function extend($name, $lifetime) {
			if (isset(self::instance()->data['__ELEMENTS'][$name])) {
				self::instance()->data['__ELEMENTS'][$name]->extend($lifetime);
			}
		}
		
		/**
		 * Loads session content from driver
		 */
		private function load() {
			$this->data = $this->driver->get();
			$this->data['__COUNT'] = count($this->data['__ELEMENTS']);
		}
		
		/**
		 * Saves session content into driver
		 */
		private function save() {
			$data = $this->data;
			unset($data['__COUNT']);
			
			$this->driver->set($data);
		}
		
		/**
		 * Protects session from hijacking
		 * 
		 * @throws \System\Core\Exception
		 */
		private function protectSession() {
			if ($this->data['__PROTECTED'] !== $this->generateProtectKey()) {
				Core::resetUUID();
				unset($this->data);
				$this->initialize();
				
				if ($this->config->error) {
					throw new Exception(I18n::translate('SESSION_HIJACKING'));
				}
			}
		}
		
		/**
		 * Initiates empty session
		 */
		private function initialize() {
			$this->data['__UUID'] =& Core::$UUID;
			
			if (!isset($this->data['__PROTECTED'])) {
				$this->data['__PROTECTED'] = $this->generateProtectKey();
				$this->data['__ELEMENTS'] = array();
				$this->data['__COUNT'] = 0;
			}
		}
		
		/**
		 * Generates session protection key
		 * 
		 * @return string Key
		 */
		private function generateProtectKey() {
			return sha1(Core::$UUID.'-'.(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''));
		}
		
		/**
		 * Clears session from expired elements
		 */
		private function cleanSession() {
			if (!empty($this->data['__USER'])) {
				Core::instance()->user->cleanUser();
			}
			
			foreach ($this->data['__ELEMENTS'] as $name=>$element) {
				if ($element->expires < time() && $element->expires > 0) {
					unset($this->data['__ELEMENTS'][$name]);
				}
			}
		}
	}
?>
