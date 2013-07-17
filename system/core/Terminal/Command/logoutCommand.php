<?php
	namespace System\Terminal\Command;
	
	class logoutCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		public function execute() {
			$session = self::$session;
			$status = $session->pull();
			
			$status->initialize();
			$status->clear = true;
			$status->buffer('Bye!');
			$status->prompt = '';
			
			$session->push($status);
		}
	}
?>