<?php
	namespace System\Terminal;
	
	/**
	 * Terminal session object
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2013, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class Session {
		/**
		 * Terminal storage name
		 * @var string
		 */
		const STORAGE = 'gOPF-TERMINAL';
		
		/**
		 * Client UUID for individual terminal instance per user
		 * @var string
		 */
		private $id;
		
		/**
		 * Terminal session storage
		 * @var \System\Storage
		 */
		private $storage;
		
		/**
		 * Client container from session storage
		 * @var array
		 */
		private $container = array();
		
		/**
		 * Initializes terminal session object
		 */
		public function __construct() {
			$this->id = \System\Core::$UUID;
			$this->storage = new \System\Storage();
			$this->read();
		}
		
		/**
		 * Allows to fast pull/set/push action
		 * 
		 * @param string $name Variable name
		 * @param mixed $value Variable value
		 */
		public function __set($name, $value) {
			$status = $this->pull();
			$status->{$name} = $value;
			$this->push($status);
		}
		
		/**
		 * Allows to falst pull/get action
		 * 
		 * @param string $name Variable name
		 * @return mixed Variable value
		 */
		public function __get($name) {
			$status = $this->pull();
			
			return $status->{$name}; 
		}
		
		/**
		 * Updates terminal status object in session storage
		 * 
		 * @param \System\Terminal\Status $value Current terminal status
		 */
		public function push(Status $value) {
			$this->container[$this->id] = $value;
			$this->write();
		}
		
		/** 
		 * Reads terminal status object from session storage
		 * 
		 * @return \System\Terminal\Status Current terminal status
		 */
		public function pull() {
			$this->read();
			return $this->container[$this->id];
		}
		
		/**
		 * Allows to fast pull/buffer/push action
		 * 
		 * @param string $content Content to buffer
		 */
		public function buffer($content) {
			$status = $this->pull();
			$status->buffer($content);
			$this->push($status);
		}
		
		/**
		 * Reads current terminal session
		 */
		private function read() {
			$this->storage->read(self::STORAGE);
			$this->container = $this->storage->get(self::STORAGE);
		}
		
		/**
		 * Writes current terminal session
		 */
		private function write() {
			$this->storage->set(self::STORAGE, $this->container);
			$this->storage->write(self::STORAGE);
		}
	}
?>