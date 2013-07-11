<?php
	namespace System\Terminal\Command;
	
	class lsCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		public function execute(\System\Terminal\Session $session) {
			$buffer = '';
			$iterator = new \DirectoryIterator(__ROOT_PATH.$session->path);
			
			foreach ($iterator as $file) {
				$buffer .= $file->getFilename()."\n";
			}
			
			$session->buffer($buffer);
		}
	}
?>