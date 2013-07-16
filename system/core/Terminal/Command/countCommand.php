<?php
	namespace System\Terminal\Command;
	
	class countCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		public function execute() {
			for ($i = 1; $i <= $this->value; $i++) {
				sleep(1);
				
				self::$session->buffer($i);
			}
		}
	}
?>