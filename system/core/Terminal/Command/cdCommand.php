<?php 
	namespace System\Terminal\Command;
	use \System\Terminal\Exception;
	use \System\Terminal\Status;
	
	class cdCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		public function execute() {
			$session = self::$session;
			
			if (empty($this->value)) {
				$session->buffer(__ROOT_PATH.$session->path);
				return;
			}
			
			$status = $session->pull();
			
			if ($this->value == '..') {
				$status = $this->moveUp($status);
			} elseif ($this->value[0] == '/') {
				$status = $this->absoluteMoveTo($status);
			} else {
				$status = $this->relativeMoveTo($status);				
			}
			
			$session->push($status);
		}
		
		private function relativeMoveTo(Status $status) {
			$path = $status->path.$this->value;
			
			if ($this->checkDirectory($path)) {
				$status->path = $this->buildPath($path);
				return $status;
			} else {
				throw new Exception('Wrong way!');
			}
		}
		
		private function absoluteMoveTo(Status $status) {
			if ($this->checkDirectory($this->value)) {
				$status->path = $this->buildPath($this->value);
				return $status;
			} else {
				throw new Exception('Wrong way!');
			}
		}
		
		private function moveUp(Status $status) {
			if ($status->path == DIRECTORY_SEPARATOR) {
				throw new Exception('You shall not pass!');
			}
			
			$exploded = explode(DIRECTORY_SEPARATOR, $status->path);
			$exploded = array_slice($exploded, 0, -2);
			$imploded = implode(DIRECTORY_SEPARATOR, $exploded);
			
			$status->path = $this->buildPath((empty($imploded)) ? DIRECTORY_SEPARATOR : $imploded); 
			return $status;
		}
		
		private function checkDirectory($directory) {
			return \System\Filesystem::checkDirectory(__ROOT_PATH.$directory);
		}
		
		private function buildPath($path) {
			if ($path[strlen($path)-1] != DIRECTORY_SEPARATOR) {
				$path .= DIRECTORY_SEPARATOR;
			}
			
			return $path;
		}
	}
?>