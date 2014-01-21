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
		 * @return \gOPF\gODI\Statement\Select
		 */
		public function select($table) {
			return new Select($this->PDO, $table);
		}
		
		public function delete($table) {
			return new Delete($this->PDO, $table);
		}
		
		public function insert($table) {
			return new Insert($this->PDO, $table);
		}
		
		public function update($table) {
			return new Update($this->PDO, $table);
		}
		
		public function raw() {
			return $this->pdo;
		}
	}
?>