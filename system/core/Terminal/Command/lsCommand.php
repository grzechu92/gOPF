<?php
	namespace System\Terminal\Command;
	
	/**
	 * Terminal command: ls (lists current directory)
	 *
	 * @author Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @copyright Copyright (C) 2011-2015, Grzegorz `Grze_chu` Borkowski <mail@grze.ch>
	 * @license The GNU Lesser General Public License, version 3.0 <http://www.opensource.org/licenses/LGPL-3.0>
	 */
	class lsCommand extends \System\Terminal\Command implements \System\Terminal\CommandInterface {
		/**
		 * @see \System\Terminal\CommandInterface::help()
		 */
		public function help() {
			$help = new \System\Terminal\Help('Prints content of current directory');
		
			return $help;
		}
		
		/**
		 * Max directory or filename length
		 * @var int
		 */
		private $length = 0;
		
		/**
		 * @see \System\Terminal\CommandInterface::execute()
		 */
		public function execute() {
			$session = self::$session;
			$buffer = '';
			
			foreach ($this->getElements($session->path) as $element) {
				$name = ($element->isDir()) ? '<strong>'.$element->getFilename().'</strong>' : $element->getFilename();
				$size = ($element->isDir()) ? '' : convertBytes($element->getSize());
				
				$buffer .= str_pad($name, $this->length+10, ' ', STR_PAD_RIGHT).$size."\n";
			}
			
			$session->buffer($buffer);
		}
		
		/**
		 * Returns array with directory elements from requested path
		 * 
		 * @param string $path Path to read
		 * @return array Array with directory elements
		 */
		public function getElements($path) {
			$files = $dirs = array();
			
			foreach (new \DirectoryIterator(__ROOT_PATH.$path) as $element) {
				/** @var $element \DirectoryIterator */
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