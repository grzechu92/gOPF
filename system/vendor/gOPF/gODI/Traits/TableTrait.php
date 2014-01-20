<?php
	namespace gOPF\gODI\Traits;
	
	trait TableTrait {
		private $table;
		
		public function table($table) {
			$this->table = $table;
				
			return $this;
		}
	}
?>