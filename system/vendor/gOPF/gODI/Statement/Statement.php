<?php
	namespace gOPF\gODI\Statement;
	use \PDO;
	use \PDOStatement;
	
	abstract class Statement {
		private $statement;
		
		public function __construct(\gOPF\gODI\Statement $statement) {
			$this->statement = $statement;
		}
		
		public function execute() {
			$query = $this->statement->raw()->prepare($this->build());
			$query->execute();
			
			return $query->fetchAll(PDO::FETCH_OBJ);
		}
	}
?>