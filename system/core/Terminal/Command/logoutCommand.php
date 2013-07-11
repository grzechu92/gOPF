<?php
	namespace System\Terminal\Command;
	
	class logoutCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		public function execute(\System\Terminal\Session $session) {
			$status = $session->get();
			
			$status->initialize();
			$status->buffer('Bye!');
			$status->prompt = '';
			
			$session->set($status);
		}
	}
?>