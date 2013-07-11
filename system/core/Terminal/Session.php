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
			$this->container[$this->id]->{$name} = $value;
			$this->write();
		}
		
		public function __get($name) {
			$this->read();
			return $this->container[$this->id]->{$name};
		}
		
		public function set(Status $value) {
			$this->container[$this->id] = $value;
			$this->write();
		}
		
		public function get() {
			$this->read();
			return $this->container[$this->id];
		}
		
		public function buffer($content) {
			$this->read();
			$this->container[$this->id]->buffer($content);
			$this->write();
		}
		
		public function checksum() {
			return sha1(json_encode($this->container[$this->id]));
		}
		
		public function read() {
			$this->storage->read(self::STORAGE);
			$this->container = $this->storage->get(self::STORAGE);
		}
		
		public function write() {
			$this->storage->set(self::STORAGE, $this->container);
			$this->storage->write(self::STORAGE);
		}
	}
?>