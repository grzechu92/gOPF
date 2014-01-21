<?php
	namespace gOPF\gODI;
	
	class Update extends Statement {
		use \gOPF\gODI\Traits\ValuesTrait;
		use \gOPF\gODI\Traits\SearchTrait;
		use \gOPF\gODI\Traits\LimitTrait;
		use \gOPF\gODI\Traits\SortTrait;
		
		public function build() {
			$parts = array(
				'UPDATE '.$this->table,
				'SET '.implode($this->values, ', '),
				(!empty($this->search) ? 'WHERE '.implode(' ', $this->search) : ''),
				(!empty($this->orderBy) ? 'ORDER BY '.implode(' ', array($this->orderBy, $this->orderType)) : ''),
				(($this->limit > 0) ? 'LIMIT '.$this->offset.', '.$this->limit : '')
			);
			
			return trim(implode(' ', $parts));
		}
		
		public function make() {
			return $this->execute(false);
		}
	}
?>