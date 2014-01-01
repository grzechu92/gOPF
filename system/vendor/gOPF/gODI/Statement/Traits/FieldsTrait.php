<?php
	namespace gOPF\gODI\Statement\Traits;
	
	trait FieldsTrait {
		private $fields = array();
		
		public function fields($fields) {
			if (is_array($fields)) {
				$this->fields = $fields;
			} else {
				$this->fields = array($fields);
			}
		}
	}
?>