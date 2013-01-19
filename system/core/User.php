<?php 
	namespace System;
	
	/**
	 * User class, allows to add/remove permissions and set/get variables
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class User extends Container {
		/**
		 * Variables container in user session
		 * @var string
		 */
		const VARIABLES = '__VARIABLES';
		
		/**
		 * Permissions container in user session
		 * @var string
		 */
		const PERMISSIONS = '__PERMISSIONS';
		
		/**
		 * Sets variables in user session
		 * @see System.Container::set()
		 */
		public function set($offset, $value) {
			$session = $this->getSession();
			
			$session[self::VARIABLES][$offset] = $value;
			
			$this->setSession($session);
		}
		
		/**
		 * Gets variables from user session
		 * @see System.Container::get()
		 */
		public function get($offset) {
			$session = $this->getSession();
			
			return (isset($session[self::VARIABLES][$offset]) ? $session[self::VARIABLES][$offset] : null); 
		}
		
		/**
		 * Removes variables from user session
		 * @param string $offset Variable name
		 */
		public function remove($offset) {
			$session = $this->getSession();
			
			if (isset($session[self::VARIABLES][$offset])) {
				unset($session[self::VARIABLES][$offset]);
			}
			
			$this->setSession($session);
		}
		
		/**
		 * Checks if user has permission
		 * 
		 * @param string $level Permission name
		 * @return bool Has permission or not
		 */
		public function is($level) {
			$session = $this->getSession();
			
			if ($level == 'guest') {
				return true;
			}
			
			return in_array($level, (empty($session[self::PERMISSIONS]) ? array() : $session[self::PERMISSIONS]));
		}
		
		/**
		 * Adds permission to user
		 * 
		 * @param string $permission Permission name
		 */
		public function addPermission($permission) {
			if (!$this->is($permission)) {
				$session = $this->getSession();
				$permissions = (isset($session[self::PERMISSIONS]) ? $session[self::PERMISSIONS] : array());
				
				$session[self::PERMISSIONS] = array_merge($permissions, array($permission));
				
				$this->setSession($session);
			}
		}
		
		/**
		 * Removes user permission
		 * 
		 * @param string $permission Permission name
		 */
		public function removePermission($permission) {
			$session = $this->getSession();
			
			if (!empty($session[self::PERMISSIONS])) {
				foreach ($session[self::PERMISSIONS] as $key => $value) {
					if ($permission == $value) {
						unset($session[self::PERMISSIONS][$key]);
					}
				}
			}
			
			$this->setSession($session);
		}
		
		/**
		 * Cleans user session from empty containers
		 */
		public function cleanUser() {
			$session = Session::get('__USER');
			
			if (isset($session)) {
				foreach (array(self::VARIABLES, self::PERMISSIONS) as $name) {
					if (!isset($session[$name]) || empty($session[$name])) {
						unset($session[$name]);
					}
				}
			}
			
			if (empty($session)) {
				Session::remove('__USER');
			} else {
				$this->setSession($session);
			}
		}
		
		/**
		 * Cleans/Removes user session
		 */
		public function cleanSession() {
			Session::remove('__USER');
		}
		
		/**
		 * Cleans user permissions
		 */
		public function cleanPermissions() {
			$session = $this->getSession();
			$session[self::PERMISSIONS] = array();
			
			$this->setSession($session);
		}
		
		/**
		 * Cleans user variables
		 */
		public function cleanVariables() {
			$session = $this->getSession();
			$session[self::VARIABLES] = array();
				
			$this->setSession($session);
		}
		
		/**
		 * Returns current user session value
		 */
		private function getSession() {
			return Session::get('__USER');
		}
		
		/**
		 * Sets user session value
		 * @param string $data New session
		 */
		private function setSession($data) {
			Session::set('__USER', $data);
		}
	}
?>