<?php
	namespace gOPF;
	
	class gODI extends \System\Database\PDO implements \System\Database\EngineInterface {
		/**
		 * Statement object
		 * @var \gOPF\gODI\Statement
		 */
		private $statement;
		
		/**
		 * @see \System\Database\EngineInterface::handler()
		 */
		public function handler() {
			$this->statement = new \gOPF\gODI\Statement($this->handler);
			return $this->statement;
		}
	}
?>