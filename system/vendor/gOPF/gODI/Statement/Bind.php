<?php
	namespace gOPF\gODI\Statement;
	use \gOPF\gODI\Statement;
	
	class Bind {
		const INT = Statement::INT;
		const BOOL = Statement::BOOL;
		const STRING = Statement::STRING;
		
		public $name;
		public $type;
		public $value;
		
		public function __construct($value, $type = self::INT) {
			$this->name = ':'.substr(sha1(rand()), 0, 9);
			$this->value = $value;
			$this->type = $type;
		}
	}
?>