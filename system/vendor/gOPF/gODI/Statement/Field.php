<?php
	namespace gOPF\gODI\Statement;
	use \gOPF\gODI\Statement;
	
	class Field {
		const INT = Statement::INT;
		const BOOL = Statement::BOOL;
		const STRING = Statement::STRING;
		
		public $name;
		public $bind;
		
		public function __construct($name, $value, $type = self::INT) {
			$this->bind = new Bind($value, $type);
			$this->name = $name;
		}
		
		public function __toString() {
			return $this->name.' = '.$this->bind->name;
		}
	}
?>