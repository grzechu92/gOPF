<?php
	namespace gOPF\gODI\Traits;
	use \gOPF\gODI\Statement\Field;
	
	trait ValuesTrait {
		private $values = array();
		
		public function field($name, $value, $type = Field::INT) {
			$field = new Field($name, $value, $type);
			$this->bind($field->bind);
			
			$this->values[] = $field;
			
			return $this;
		}
	}
?>