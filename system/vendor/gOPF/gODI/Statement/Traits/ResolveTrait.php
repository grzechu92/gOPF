<?php
	namespace gOPF\gODI\Statement\Traits;
	
	trait ResolveTrait {
		private $limit = 0;
		
		public function all() {
			return $this->execute();
		}
		
		public function get($amount) {
			$this->limit = $limit;
			$this->execute();
		}
	}
?>