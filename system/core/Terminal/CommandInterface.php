<?php
	namespace System\Terminal;
	
	interface CommandInterface {
		public function extend(Command $command);
		public function execute(Session $session);
	}
?>