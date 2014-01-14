<?php
	namespace gOPF\gODI;
	
	/**
	 * 
	 * @author grzechu
	 *
	 */
	class Handler {
		/**
		 * 
		 * @var \PDO
		 */
		private $PDO;
		
		public function __construct(\PDO $PDO) {
			$this->PDO = $PDO;
		}
		
		/**
		 * 
		 * @param string $fields
		 * @return \gOPF\gODI\Statement\Select
		 */
		public function select($fields = '*') {
			return (new Select($this->PDO))->fields($fields);
		}
		
		public function delete() {
			return new Delete($this->PDO);
		}
		
		public function insert() {
			
		}
		
		public function update() {
			
		}
		
		public function raw() {
			return $this->pdo;
		}
	}
?>