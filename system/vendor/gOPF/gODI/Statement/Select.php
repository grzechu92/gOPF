<?php
	namespace gOPF\gODI\Statement;
	
	class Select extends Statement implements StatementInterface {
		use \gOPF\gODI\Statement\Traits\FieldsTrait;
		use \gOPF\gODI\Statement\Traits\TableTrait; 
		use \gOPF\gODI\Statement\Traits\ResolveTrait;
		
		public function resolve() {
			return $this->execute();
		}
		
		public function build() {
			$parts = array(
				'SELECT '.implode(', ', $this->fields),
				'FROM '.$this->table  
			);
			
			return trim(implode(' ', $parts));
		}
	}
?>