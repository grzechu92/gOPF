<?php 
	namespace System\Terminal\Command;
	
	class sha1Command extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		public function execute(\System\Terminal\Session $session) {
			$session->buffer(sha1($this->value));
		}
	}
?>