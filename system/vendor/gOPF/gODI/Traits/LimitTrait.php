<?php
	namespace gOPF\gODI\Traits;
	
	trait LimitTrait {
		private $limit = 0;
		private $offset = 0;
		
		public function limit($limit, $offset = 0) {
			$this->limit = $limit;
			$this->offset = $offset;
				
			return $this;
		}
	}
?>