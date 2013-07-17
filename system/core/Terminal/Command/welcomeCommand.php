<?php
	namespace System\Terminal\Command;
	
	class welcomeCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		public function execute() {
			$session = self::$session;
			
			$message = "\n".str_pad('gOPF Terminal v'.\System\Core::VERSION, 50, ' ', STR_PAD_BOTH)."\n";
			$message .= "\n".str_pad('Blablabla. There is no help command, yet.', 50, ' ', STR_PAD_BOTH)."\n";
			
			$session->clear = true;
			$session->buffer($message);
		}
	}
?>