<?php
	namespace System\Terminal;
	
	class Status {
		const PASSWORD = 'password';
		const TEXT = 'text';
		
		public $user;
		public $prompt;
		public $host;
		public $path;
		public $buffer;
		public $prefix;
		public $type = self::TEXT;
		public $initialized = false;
		public $logged = false;
		public $processing = true;
		public $clear = false;
		public $abort = false;
		public $updated;
		
		public function initialize() {
			$this->user = $_SERVER['REMOTE_ADDR'];
			$this->host = $_SERVER['HTTP_HOST'];
			$this->initialized = true;
			$this->path = '/';
			$this->logged = false;
			$this->processing = true;
			$this->clear = false;
			$this->abort = false;
		}
		
		public function buffer($content) {
			$this->buffer .= $content."\n";
		}
		
		public function checksum() {
			return sha1(json_encode($this));
		}
	}
?>