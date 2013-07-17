<?php
	namespace System\Terminal;
	
	class Session {
		const STORAGE = 'gOPF-TERMINAL';
		
		private $id;
		private $storage;
		private $container = array();
		
		public function __construct() {
			$this->id = \System\Core::$UUID;
			$this->storage = new \System\Storage();
			$this->read();
		}
		
		public function __set($name, $value) {
			$status = $this->pull();
			$status->{$name} = $value;
			$this->push($status);
		}
		
		public function __get($name) {
			$status = $this->pull();
			
			return $status->{$name}; 
		}
		
		public function push(Status $value) {
			$this->container[$this->id] = $value;
			$this->write();
		}
		
		/** 
		 * @return \System\Terminal\Status
		 */
		public function pull() {
			$this->read();
			return $this->container[$this->id];
		}
		
		public function buffer($content) {
			$status = $this->pull();
			$status->buffer($content);
			
			$this->push($status);
		}
		
		private function read() {
			$this->storage->read(self::STORAGE);
			$this->container = $this->storage->get(self::STORAGE);
		}
		
		private function write() {
			$this->storage->set(self::STORAGE, $this->container);
			$this->storage->write(self::STORAGE);
		}
	}
?>