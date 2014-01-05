<?php
	namespace gOPF\gODI\Traits;
	use \gOPF\gODI\Statement\Where;
	
	trait SearchTrait {
		private $search = array();
		
		/**
		 * 
		 * @param string $field
		 * @return \gOPF\gODI\Statement\Where
		 */
		public function where($field) {
			return new Where($this, $this->search, $field);
		}
		
		public function orWhere($field) {
			return new Where($this, $this->search, $field, 'OR');
		}
		
		public function andWhere($field) {
			return new Where($this, $this->search, $field, 'AND');
		}
	}
?>