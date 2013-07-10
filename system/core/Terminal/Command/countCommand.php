<?php
	namespace System\Terminal\Command;
	
	class countCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		public function execute(\System\Terminal\Session $session) {
			for ($i = 1; $i <= $this->value; $i++) {
				sleep(1);
				
				$session->buffer($i);
			}
		}
	}
?>