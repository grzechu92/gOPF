<?php
	namespace gOPF\gODI\Statement;
	
	interface StatementInterface {
		public function __construct(\gOPF\gODI\Statement $statement);
		public function build();
		public function execute();
	}
?>