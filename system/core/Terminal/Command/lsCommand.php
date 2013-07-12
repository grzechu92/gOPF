<?php
	namespace System\Terminal\Command;
	
	class lsCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		private $length;
		
		public function execute(\System\Terminal\Session $session) {
			$buffer = '';
			
			foreach ($this->getElements($session->path) as $element) {
				$name = ($element->isDir()) ? '<strong>'.$element->getFilename().'</strong>' : $element->getFilename();
				$size = ($element->isDir()) ? '' : convertBytes($element->getSize());
				
				$buffer .= str_pad($name, $this->length+10, ' ', STR_PAD_RIGHT).$size."\n";
			}
			
			$session->buffer($buffer);
		}
		
		public function getElements($path) {
			$iterator = new \DirectoryIterator(__ROOT_PATH.$path);
			$files = $dirs = array();
			
			foreach ($iterator as $element) {
				if ($element->isDot()) continue;
			
				if ($element->isFile()) {
					$files[$element->getFilename()] = clone($element);
				} else {
					$dirs[$element->getFilename()] = clone($element);
				}
			
				if ($this->length < strlen($element->getFilename())) {
					$this->length = strlen($element->getFilename());
				}
			}
				
			ksort($files);
			ksort($dirs);
			
			return array_merge($dirs, $files);
		}
	}
?>