<?php
	namespace gOPF\gODI\Statement;
	
	class Bind {
		public $name;
		public $type;
		public $value;
		
		public function __construct($value, $type) {
			$this->name = ':'.substr(sha1(rand()), 0, 9);
			$this->value = $value;
			$this->type = $type;
		}
	}
?>