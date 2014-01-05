<?php
	namespace gOPF\gODI\Traits;
	
	trait TableTrait {
		private $table;
		
		/**
		 * 
		 * 
		 * @param string $table Table name
		 * @return \gOPF\gODI\Statement\Statement;
		 */
		public function from($table) {
			$this->table = $table;
			
			return $this;
		}
		
		public function table($table) {
			$this->table = $table;
				
			return $this;
		}
		
		public function into($table) {
			$this->table = $table;
		
			return $this;
		}
	}
?>