<?php
	namespace gOPF\gODI;
	use \gOPF\gODI\Statement\Select;
	
	class Statement {
		private $handler;
		
		public function __construct(\PDO $handler) {
			$this->handler = $handler;
		}
		
		public function select($fields = '*') {
			$statement = new Select($this);
			$statement->fields($fields);
			
			return $statement;
		}
		
		public function delete() {
			
		}
		
		public function insert() {
			
		}
		
		public function update() {
			
		}
		
		public function raw() {
			return $this->handler;
		}
	}
?>