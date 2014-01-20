<?php
	namespace gOPF\gODI;
	
	class Select extends Statement {
		use \gOPF\gODI\Traits\FieldsTrait;
		use \gOPF\gODI\Traits\TableTrait; 
		use \gOPF\gODI\Traits\SearchTrait;
		use \gOPF\gODI\Traits\LimitTrait;
		use \gOPF\gODI\Traits\SortTrait;
		
		public function build() {
			$parts = array(
				'SELECT '.implode(', ', $this->fields),
				'FROM '.$this->table,
				(!empty($this->search) ? 'WHERE '.implode(' ', $this->search) : ''),
				(!empty($this->orderBy) ? 'ORDER BY '.implode(' ', array($this->orderBy, $this->orderType)) : ''),
				(($this->limit > 0) ? 'LIMIT '.$this->offset.', '.$this->limit : '')
			);
			
			return trim(implode(' ', $parts));
		}
		
		public function all() {
			return $this->execute(true);
		}
		
		public function get($limit = 1, $offset = 0) {
			$this->limit($limit, $offset);
				
			return $this->execute(true);
		}
	}
?>