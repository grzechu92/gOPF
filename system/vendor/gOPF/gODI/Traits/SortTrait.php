<?php
	namespace gOPF\gODI\Traits;
	
	trait SortTrait {
		private $orderBy;
		private $orderType;
		
		public function by($field, $type = 'ASC') {
			$this->orderBy = $field;
			$this->orderType = $type;
			
			return $this;
		}
	}
?>