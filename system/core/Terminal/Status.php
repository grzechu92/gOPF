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
		
		public function initialize() {
			$this->user = $_SERVER['REMOTE_ADDR'];
			$this->host = $_SERVER['HTTP_HOST'];
			$this->path = '/';
			$this->initialized = true;
			$this->logged = false;
			$this->processing = true;
		}
		
		public function buffer($content) {
			$this->buffer .= $content."\n";
		}
	}
?>