<?php
	namespace gOPF\gODI\Statement\Traits;
	
	trait TableTrait {
		private $table;
		
		public function from($table) {
			$this->table = $table;
			
			return $this;
		}
	}
?>