<?php
	namespace gOPF\gODI;
	use \PDO;
	
	abstract class Statement {
		const INT = PDO::PARAM_INT;
		const STRING = PDO::PARAM_STR;
		const BOOL = PDO::PARAM_BOOL;
		const ASC = 'ASC';
		const DESC = 'DESC';
		
		private $PDO;
		private $bind = array();
		
		final public function __construct(PDO $PDO) {
			$this->PDO = $PDO;
		}
		
		final public function __toString() {
			return $this->build();
		}
		
		final public function bind(\gOPF\gODI\Statement\Bind $bind) {
			$this->bind[] = $bind;
		}
		
		final protected function execute($values) {
			$query = $this->PDO->prepare($this->build());
			
			foreach ($this->bind as $bind) {
				$query->bindValue($bind->name, $bind->value, $bind->type);
			}
			
			$query->execute();
			
			if ($values) {
				$result = $query->fetchAll(PDO::FETCH_OBJ);
				
				if (count($result) == 1) {
					return $result[0];
				} else {
					return $result;
				}
			} else {
				return $query->rowCount();
			}
		}
	}
?>