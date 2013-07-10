<?php 
	namespace System\Terminal\Command;
	use \System\Terminal\Session;
	use \System\Terminal\Exception;
	
	class cdCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		private $session;
		
		public function execute(Session $session) {
			$this->session = $session;
			
			if (empty($this->value)) {
				$session->buffer(__ROOT_PATH.$session->path);
				return;
			}
			
			if ($this->value == '..') {
				$this->moveUp();
				return;
			}
			
			if ($this->value[0] == '/') {
				$this->absoluteMoveTo();
				return;
			}
			
			$this->relativeMoveTo();
		}
		
		private function relativeMoveTo() {
			$path = $this->session->path.$this->value;
			
			if ($this->checkDirectory($path)) {
				$this->setPath($path);
			} else {
				throw new Exception('Wrong way!');
			}
		}
		
		private function absoluteMoveTo() {
			if ($this->checkDirectory($this->value)) {
				$this->setPath($this->value);
			} else {
				throw new Exception('Wrong way!');
			}
		}
		
		private function moveUp() {
			$session = $this->session;
			
			if ($session->path == DIRECTORY_SEPARATOR) {
				throw new Exception('You shall not pass!');
			}
			
			$exploded = explode(DIRECTORY_SEPARATOR, $session->path);
			$exploded = array_slice($exploded, 0, -2);
			$imploded = implode(DIRECTORY_SEPARATOR, $exploded);
			
			$this->setPath((empty($imploded)) ? DIRECTORY_SEPARATOR : $imploded);
		}
		
		private function checkDirectory($directory) {
			return \System\Filesystem::checkDirectory(__ROOT_PATH.$directory);
		}
		
		private function setPath($path) {
			if ($path[strlen($path)-1] != DIRECTORY_SEPARATOR) {
				$path .= DIRECTORY_SEPARATOR;
			}
			
			$this->session->path = $path;
		}
	}
?>