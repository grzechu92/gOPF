<?php
	namespace gOPF\gODI;
	
	class Select extends Statement {
		use \gOPF\gODI\Traits\FieldsTrait;
		use \gOPF\gODI\Traits\TableTrait; 
		use \gOPF\gODI\Traits\SearchTrait;
		use \gOPF\gODI\Traits\ResolveTrait;
		
		public function build() {
			$parts = array(
				'SELECT '.implode(', ', $this->fields), //SELECT something
				'FROM '.$this->table, //FROM somewhere
				(!empty($this->search) ? 'WHERE '.implode(' ', $this->search) : ''),
				(($this->limit > 0) ? 'LIMIT '.$this->offset.', '.$this->limit : '') //LIMIT amount,
			);
			
			return trim(implode(' ', $parts));
		}
	}
?>