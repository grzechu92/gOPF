<?php
	namespace gOPF\gODI;
	
	class Delete extends Statement {
		use \gOPF\gODI\Traits\SearchTrait;
		use \gOPF\gODI\Traits\SortTrait;
		use \gOPF\gODI\Traits\LimitTrait;
		
		public function build() {
			$parts = array(
				'DELETE',
				'FROM '.$this->table,
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