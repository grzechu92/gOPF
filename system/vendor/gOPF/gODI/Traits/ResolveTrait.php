<?php
	namespace gOPF\gODI\Traits;
	
	trait ResolveTrait {
		private $limit = 0;
		private $offset = 0;
		
		public function all() {
			return $this->execute();
		}
		
		public function exec() {
			$this->execute();
		}
		
		public function get($limit = 1, $offset = 0) {
			$this->limit = $limit;
			$this->offset = $offset;
			
			return $this->execute();
		}
	}
?>