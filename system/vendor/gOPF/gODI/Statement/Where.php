<?php
	namespace gOPF\gODI\Statement;
	use \gOPF\gODI\Statement;
	
	class Where {
		private $prefix;
		private $field;
		private $list;
		
		/**
		 * 
		 * @var \gOPF\gODI\Statement
		 */
		private $statement;
		
		public function __construct(Statement $statement, &$list, $field, $prefix = null) {
			$this->statement = $statement;
			$this->list = &$list;
			$this->field = $field;
			$this->prefix = $prefix;
		}
		
		public function eq($value, $type = Statement::INT) {
			return $this->set('=', $value, $type);
		}
		
		public function not($value, $type = Statement::INT) {
			return $this->set('<>', $value, $type);
		}
		
		public function gt($value, $type = Statement::INT) {
			return $this->set('>', $value, $type);
		} 
		
		public function lt($value, $type = Statement::INT) {
			return $this->set('<', $value, $type);
		}
		
		public function gte($value, $type = Statement::INT) {
			return $this->set('>=', $value, $type);
		}
		
		public function lte($value, $type = Statement::INT) {
			return $this->set('<=', $value, $type);
		}
		
		public function like($value, $type = Statement::STRING) {
			return $this->set('LIKE', $value, $type);
		}
		
		public function is($value, $type = Statement::STRING) {
			return $this->set('IS', $value, $type);
		}
		
		private function set($operator, $value, $type) {
			$bind = new Bind($value, $type);
			
			$this->list[] = trim(implode(' ', array($this->prefix, $this->field, $operator, $bind->name)));
			$this->statement->bind($bind);
			
			return $this->statement;
		}
	}
?>